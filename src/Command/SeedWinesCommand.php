<?php

namespace App\Command;

use App\Entity\Wine;
use App\Entity\WineCategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:seed-wines',
    description: 'Popola le categorie e i vini della carta (idempotente).',
)]
class SeedWinesCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $repo = $this->em->getRepository(WineCategory::class);

        $data = [
            ['name' => 'Vini Rossi', 'slug' => 'rossi', 'priority' => 1, 'wines' => self::VINI_ROSSI],
            ['name' => 'Vini Bianchi', 'slug' => 'bianchi', 'priority' => 2, 'wines' => self::VINI_BIANCHI],
            ['name' => 'Bollicine', 'slug' => 'bollicine', 'priority' => 3, 'wines' => self::BOLLICINE],
        ];

        foreach ($data as $row) {
            $category = $repo->findOneBy(['slug' => $row['slug']]);

            if (!$category) {
                $category = (new WineCategory())
                    ->setName($row['name'])
                    ->setSlug($row['slug'])
                    ->setPriority($row['priority']);
                $this->em->persist($category);
            } elseif ($category->getWines()->count() > 0) {
                $io->writeln(sprintf('Categoria "%s" gia popolata, salto.', $row['name']));
                continue;
            }

            $priority = 1;
            foreach ($row['wines'] as $name) {
                $wine = (new Wine())
                    ->setName($name)
                    ->setPriority($priority++)
                    ->setYear(self::extractYear($name))
                    ->setCategory($category);
                $this->em->persist($wine);
            }

            $io->writeln(sprintf('Categoria "%s": %d vini.', $row['name'], count($row['wines'])));
        }

        // Backfill: assegna l'anno ai vini gia presenti che non lo hanno.
        $backfilled = 0;
        foreach ($this->em->getRepository(Wine::class)->findAll() as $wine) {
            if ($wine->getYear() === null && ($year = self::extractYear($wine->getName())) !== null) {
                $wine->setYear($year);
                $backfilled++;
            }
        }
        if ($backfilled > 0) {
            $io->writeln(sprintf('Anno assegnato a %d vini gia presenti.', $backfilled));
        }

        $this->em->flush();
        $io->success('Carta dei vini popolata.');

        return Command::SUCCESS;
    }

    /**
     * Estrae l'annata (4 cifre, 1900-2099) dal nome del vino; l'ultima trovata,
     * dato che l'annata di solito compare verso la fine del nome.
     */
    private static function extractYear(string $name): ?int
    {
        if (preg_match_all('/\b(?:19|20)\d{2}\b/', $name, $matches) && $matches[0]) {
            return (int) end($matches[0]);
        }

        return null;
    }

    private const VINI_ROSSI = [
        'A.A. Lagrein DOC 2024',
        'A.A. Pinot Nero DOC 2024',
        'A.A. Pinot Nero Riserva Glen DOC 2022',
        "Aglianico d'Irpinia i.g.t. 2021",
        'Amarone della Valpolicella DOCG 2021 Marne 180',
        'Aric Amel Langhe Nebbiolo 2024',
        'Barbaresco DOCG 2022 Rocche dei 7 Fratelli',
        "Barbera d'Alba DOC 2022 Lorens",
        "Barbera d'Alba Ruvei Doc 2022",
        "Barbera d'Asti Rure' Docg 2023",
        'Barolo DOCG 2020 Bricco Ambrogio Lorens',
        'Capoarso Perricone igt 2022',
        'Chianti Classico d.o.c.g. 2023',
        'Chianti Classico d.o.c.g. Riserva 2022',
        'Chianti Classico d.o.c.g. Rocca delle Magie 2023',
        "Dolcetto d'Alba DOC 2024",
        "Dolcetto d'Alba Madonna del Dono 2023 Doc",
        'Finimondo! 2024',
        'Fontanelle Langhe Barbera 2023',
        'Junco Frappato igt 2023',
        'Mandus - Primitivo di Manduria d.o.c. 2023',
        'Morellino di Scansano d.o.c.g. 2024',
        "Nebbiolo d'Alba DOC 2024",
        "Nebbiolo d'Alba Michet Doc 2021",
        "Negroamaro 'Nterra Puglia igp 2024",
        'Petra Alto Toscana IGT Sangiovese Rosso 2021',
        'Petra Hebo Toscana IGT Rosso 2024',
        'Petra Potenti Toscana IGT Cabernet Sauvignon Rosso 2023',
        'Pietra Pura - Hyria Primitivo IGP',
        'Rubizzo Rosso di Toscana I.G.T. 2024',
        'San Giacomo Langhe Nebbiolo 2023',
        'Sella&Mosca Dimonios Cannonau di Sardegna Riserva DOC 2022',
        'Sella&Mosca Medeus Cannonau di Sardegna DOC 2024',
        'Sella&Mosca Mustazzo Cannonau di Sardegna DOC 2021',
        "Sella&Mosca Tanca Farra' Alghero Rosso DOC 2022",
        'Tanaurpi Malbec igt 2023',
        "Tareni Nero d'Avola doc 2023",
        'Tareni Syrah igt 2023',
        'Tripudium Rosso 2022',
        'Valpolicella DOC Classico Superiore La Fabriseria 2021',
        "Valpolicella DOC Superiore 2022 Capitel Nicalo'",
        'Valpolicella DOC Superiore Maternigo 2021',
        'Valpolicella Ripasso DOC Capitel San Rocco 2022',
        'Villa Vescovile Merlot - Trentino d.o.c. 2022',
        'Villa Vescovile Pinot Nero - Trentino d.o.c. 2023',
    ];

    private const VINI_BIANCHI = [
        'A.A. Chardonnay DOC 2024 Caliz',
        'A.A. Gewurztraminer DOC 2024 Arenis',
        'A.A. Gewurztraminer Ris. Brenntal DOC 2023',
        'A.A. Kerner DOC 2024',
        'A.A. Muller Thurgau DOC 2024',
        'A.A. Pinot Bianco DOC 2024',
        'A.A. Pinot Grigio DOC 2024',
        'A.A. Sauvignon DOC 2024',
        'Alba Doc PI CIT',
        'Langhe DOC Sauvignon Bric Amel 2024',
        'Bellavista Convento SS Annunciata 2019 Curtefranca DOC Bianco',
        'Campo Maccione Vermentino i.g.t. Toscana 2024',
        'Coero Arneis Docg 2023',
        'Cristina Ascheri Langhe Arneis 2024',
        'Falanghina i.g.t. Beneventano 2024',
        'Fiano di Avellino d.o.c.g. 2024',
        'Friulano Fantin Nodar',
        'Ga.Ry IGT Veneto Bianco 2023',
        'Gibele - Zibibbo igt 2024',
        'Greco di Tufo d.o.c.g. 2024',
        'Iseis - Zibibbo Pantelleria DOC 2024',
        'Kelbi - Catarratto igt 2024',
        'Langhe DOC Chardonnay 2024',
        'Langhe DOC Chardonnay 2024 Lorens',
        'Monteleone Anthemis Etna Bianco 2023',
        'Monteleone Etna Bianco 2024',
        'Moonlite Chardonnay Toscana i.g.t. 2024',
        "Olivier Leflaive Aligote' Bourgogne 2022",
        'Olivier Leflaive Cuvee Margot Bourgogne 2022',
        'Picolit Fantin Nodar',
        'Ribolla Gialla Fantin Nodar',
        'Roero DOCG Arneis 2024',
        'Sasyri i.g.t. 2023',
        'Savi del Comune di Gavi Docg 2023',
        'Savi Docg 2023',
        'Sella&Mosca Monteoro 2024 Vermentino di Gallura Superiore',
        'Sella&Mosca Parallelo 41 DOC Alghero Bianco 2024',
        'Sella&Mosca Terre Bianche 2024 Alghero Torbato',
        'Senaria Grillo Superiore doc 2023',
        'Tareni Inzolia igt 2023',
        'Vernaccia di San Gimignano d.o.c.g. 2024',
        'Villa Vescovile Gewurztraminer - Trentino d.o.c. 2024',
        'Villa Vescovile Sauvignon - Trentino d.o.c. 2024',
    ];

    private const BOLLICINE = [
        'Bellavista Alma Assemblage2 Franciacorta Extra Brut',
        'Bellavista Alma Non Dosato Franciacorta Dosaggio Zero',
        "Bellavista Alma Rose' Franciacorta Extra Brut Rose'",
        'Bellavista Brut Teatro alla Scala 2021 Franciacorta Brut',
        'Bellavista Nectar Franciacorta Demi Sec',
        'Bianco-Bianco Charmat Brut',
        'Cofanetto da 1 Bt Giulio Ferrari 2015',
        'Contadi Blanc 2020 Franciacorta Extra Brut',
        'Contadi Brut Franciacorta Brut',
        'Contadi Pino Nero Riserva 2017 Franciacorta Dosaggio Zero Riserva',
        'Contadi Saten 2021 Franciacorta Brut Saten',
        "Ferrari Perle' millesimato 2019",
        "Ferrari Perle' Nero millesimato 2018",
        'Ferrari Riserva Lunelli millesimato 2016',
        'Grand Famille - Spumante Metodo Classico Brut',
        'Lucimare - Spumante Brut Charmat igt',
        'Magnum Giulio Ferrari Riserva Mill. 2015 - L 1.50 Ca Legno',
        "Sella&Mosca Oscari' 2022 Alghero Torbato Spumante Brut Metodo Classico",
        'Valentin Leflaive Sigma20 4.0 Champagne Extra Brut Blanc de Blancs',
        'Vino Spumante Brut Bianco',
        'V.S/Valdobb. Pros. Sup. DOCG Rive Dry 2024',
        'VSAQ/Valdobb. Pros. Sup. DOCG Brut Vedova',
        'VSAQ/Valdobb. Pros. Sup. DOCG E.Dry Vedova',
    ];
}
