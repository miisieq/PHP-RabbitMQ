<?php

declare(strict_types=1);

namespace App;

use Knp\Snappy\GeneratorInterface;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConsumerCommand extends Command
{
    private int $consumed = 0;

    private AbstractConnection $connection;

    private GeneratorInterface $pdfGenerator;

    private OutputInterface $output;

    public function __construct(AbstractConnection $connection, GeneratorInterface $pdfGenerator)
    {
        $this->connection = $connection;
        $this->pdfGenerator = $pdfGenerator;

        parent::__construct();
    }

    public function configure(): void
    {
        $this->setName('app:consume');
        $this->setDescription('Consume elements from the queue and generate PDFs.');
        $this->addArgument('consumeLimit', InputArgument::OPTIONAL, 'Number of elements to generate.', 0);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;
        $consumeLimit = (int)$input->getArgument('consumeLimit');
        $channel = $this->connection->channel();
        $channel->basic_consume('PdfGenerator', '', false, false, false, false, [$this, 'consume']);

        while (count($channel->callbacks)) {
            if ($consumeLimit > 0
                && $this->consumed >= $consumeLimit
            ) {
                $channel->close();

                break;
            }

            $channel->wait();
        }

        $channel->close();

        return 0;
    }

    public function consume(AMQPMessage $message): void
    {
        $data = json_decode($message->getBody());
        $filePath = $this->generateFilePath($data->uuid);
        $this->output->writeln("Consuming \"{$data->uuid}\"...");
        try {
            $this->pdfGenerator->generateFromHtml($data->content, $filePath);

        } catch (\Exception $e) {
            var_dump($e);exit;
        }
        $this->output->writeln("File successfully saved in \"$filePath\"!");
        $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
        $this->consumed++;
    }

    private function generateFilePath(string $uuid): string
    {
        return 'var/' . $uuid . '.pdf';
    }
}
