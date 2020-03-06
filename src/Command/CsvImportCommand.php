<?php

namespace App\Command;


use App\Entity\Campus;
use App\Entity\Participant;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class CsvImportCommand
 * @package AppBundle\ConsoleCommand
 */

class CsvImportCommand extends Command
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;

    }

    /**
     * Configure
     * @throws InvalidArgumentException
     */
    protected function configure()
    {
        $this
            ->setName('csv:import')
            ->setDescription('Import du fichier csv')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     * @throws \League\Csv\Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('en attente d\'import');
        $reader = Reader::createFromPath('%kernel.root_dir%/../src/Data/MOCK_DATA.csv');
//        ('%kernel.root_dir%/../src/Data/MOCK_DATA.csv')
        $reader->setDelimiter(",");
        $results = $reader->getRecords();

        foreach ($results as $row) {

            $io->comment("TEST" . $row[0]);

            $participant = (new Participant())
                ->setActif($row[0])
                ->setAdministrateur($row[1])
                ->setMail($row[2])
                ->setNom($row[3])
                ->setMotPasse($row[4])
                ->setPseudo($row[5])
                ->setPrenom($row[6])
                ->setTelephone($row[7]);


            $this->em->persist($participant);

            $campus = $this->em->getRepository(Campus::class)->find(1);

            $participant->setCampus($campus);
        }

        $this->em->flush();



        $io->success('La commande s\'est exécutée proprement');
    }
}