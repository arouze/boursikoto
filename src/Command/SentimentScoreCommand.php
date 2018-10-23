<?php

namespace App\Command;

use App\Entity\Mention;
use App\Repository\MentionRepository;
use App\Service\MentionService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SentimentScoreCommand extends Command
{
    private $mentionRepository;

    private $mentionService;

    public function __construct(MentionRepository $mentionRepository, MentionService $mentionService)
    {
        parent::__construct();
        $this->mentionRepository = $mentionRepository;
        $this->mentionService = $mentionService;

    }

    protected function configure() {
        $this
            ->setName('app:sentiment-score')
            ->setDescription('Will add sentiment_score and magnitude_score on mention with missing score. Will ignore some of them')
            ->setHelp('Will add sentiment_score and magnitude_score on mention');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $mentions = $this->mentionRepository->getAllMentionsWithoutSentimentScore();

        /** @var Mention $mention */
        foreach ($mentions as $mention) {
            $output->writeln(sprintf("Analyse %s", $mention->getId()));
            $this->mentionService->analyse($mention->getId());
        }
    }
}
