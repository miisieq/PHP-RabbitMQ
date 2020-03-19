<?php

declare(strict_types=1);

namespace App;

use joshtronic\LoremIpsum;
use PhpAmqpLib\Channel\AbstractChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProducerCommand extends Command
{
    private AbstractConnection $connection;

    private LoremIpsum $textGenerator;

    private ?AbstractChannel $channel;

    public function __construct(AbstractConnection $connection)
    {
        $this->connection = $connection;
        $this->textGenerator = new LoremIpsum();

        parent::__construct();
    }

    public function configure(): void
    {
        $this->setName('app:produce');
        $this->setDescription('Produce example content to the queue.');
        $this->addArgument('numberOfElements', InputArgument::REQUIRED, 'Number of elements to generate.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $numberOfElements = (int)$input->getArgument('numberOfElements');

        $this->channel = $this->connection->channel();

        $uuids = [];

        for ($i = 0; $i < $numberOfElements; $i++) {
            $uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();

            if (in_array($uuid, $uuids)) {
                throw new \RuntimeException($uuid);
            }

            $uuids[] = $uuid;
            $output->writeln("Publishing \"$uuid\"...");
            $this->publish($uuid, $this->generateDummyContent());
            $output->writeln("Message \"$uuid\" successfully published!");
        }

        $this->channel->close();

        return 0;
    }

    private function generateDummyContent(): string
    {
        return '<h1>Lorem Ipsum</h1>' . $this->textGenerator->sentences(3, ['article', 'p']);
    }

    private function publish(string $uuid, string $content): void
    {
        $this->channel->basic_publish(
            new \PhpAmqpLib\Message\AMQPMessage(\json_encode([
                'uuid' => $uuid,
                'content' => $content,
            ])),
            '',
            'PdfGenerator'
        );
    }
}
