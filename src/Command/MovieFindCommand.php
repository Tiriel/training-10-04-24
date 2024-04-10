<?php

namespace App\Command;

use App\Entity\Movie;
use App\Movie\Search\Enum\SearchType;
use App\Movie\Search\Provider\MovieProvider;
use App\Movie\Search\Provider\TraceableCliMovieProvider;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsCommand(
    name: 'app:movie:find',
    description: 'Add a short description for your command',
)]
class MovieFindCommand extends Command
{
    private ?string $value = null;
    private ?SearchType $type = null;
    private ?SymfonyStyle $io = null;

    public function __construct(
        private readonly MovieProvider $provider,
    ){
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('value', InputArgument::OPTIONAL, 'The title or IMDb ID you are searching for')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->value = $input->getArgument('value');
        $this->io = new SymfonyStyle($input, $output);
        $this->provider->setIo($this->io);

        if ($this->value) {
            $this->type = $this->getType();
        }
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        if (!$this->value) {
            $question = new Question('What is the title or IMDb ID you are searching for ?');
            $question
                ->setValidator(function (?string $answer) {
                    if ('' === $answer || null === $answer) {
                        throw new \InvalidArgumentException('The value cannot be empty!');
                    }
                    return $answer;
                })
                ->setMaxAttempts(3);
            $this->value = $this->io->askQuestion($question);
            $this->type = $this->getType();
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io->title(sprintf('You are searching for a movie with %s "%s"', $this->type->name, $this->value));

        try {
            $movie = $this->provider->getOne($this->type, $this->value);
        } catch (\RuntimeException $e) {
            $this->io->error('Unknown error : '.$e->getMessage());

            return Command::FAILURE;
        }

        if (null === $movie) {
            $this->io->error('Movie not found!');

            return Command::FAILURE;
        }

        $this->displayTable($movie);
        $this->io->success('Movie in database!');

        return Command::SUCCESS;
    }

    private function getType(): SearchType
    {
        return 0 !== preg_match('/tt\d{6,8}/i', $this->value) ? SearchType::Id : SearchType::Title;
    }

    private function displayTable(Movie $movie): void
    {
        $this->io->table(
            ['Id', 'IMDb Id', 'Title', 'Rated'],
            [[$movie->getId(), $movie->getImdbId(), $movie->getTitle(), $movie->getRated()]]
        );
    }
}
