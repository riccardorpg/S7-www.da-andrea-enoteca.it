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
                $io->writeln(sprintf('Categoria "%s" gia popolata, salto la creazione.', $row['name']));
                continue;
            }

            $priority = 1;
            foreach ($row['wines'] as [$name, $price]) {
                $wine = (new Wine())
                    ->setName($name)
                    ->setPriority($priority++)
                    ->setYear(self::extractYear($name))
                    ->setPrice($price)
                    ->setCategory($category);
                $this->em->persist($wine);
            }

            $io->writeln(sprintf('Categoria "%s": %d vini.', $row['name'], count($row['wines'])));
        }

        // Backfill: assegna anno e prezzo ai vini gia presenti, accoppiando per nome.
        $prices = self::priceMap();
        $backfilledYear = 0;
        $backfilledPrice = 0;
        foreach ($this->em->getRepository(Wine::class)->findAll() as $wine) {
            if ($wine->getYear() === null && ($year = self::extractYear((string) $wine->getName())) !== null) {
                $wine->setYear($year);
                $backfilledYear++;
            }
            if ($wine->getPrice() === null && isset($prices[$wine->getName()])) {
                $wine->setPrice($prices[$wine->getName()]);
                $backfilledPrice++;
            }
        }
        if ($backfilledYear > 0) {
            $io->writeln(sprintf('Anno assegnato a %d vini gia presenti.', $backfilledYear));
        }
        if ($backfilledPrice > 0) {
            $io->writeln(sprintf('Prezzo assegnato a %d vini gia presenti.', $backfilledPrice));
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

    /**
     * Mappa nome => prezzo, usata per il backfill dei vini gia presenti in DB.
     *
     * @return array<string, string|null>
     */
    private static function priceMap(): array
    {
        $map = [];
        foreach ([self::VINI_ROSSI, self::VINI_BIANCHI, self::BOLLICINE] as $group) {
            foreach ($group as [$name, $price]) {
                $map[$name] = $price;
            }
        }

        return $map;
    }

    private const VINI_ROSSI = [
        ['A.A. Lagrein DOC 2024', '26.00'],
        ['A.A. Pinot Nero DOC 2024', '28.00'],
        ['A.A. Pinot Nero Riserva Glen DOC 2022', '37.00'],
        ["Aglianico d'Irpinia i.g.t. 2021", '26.00'],
        ['Amarone della Valpolicella DOCG 2021 Marne 180', '50.00'],
        ['Aric Amel Langhe Nebbiolo 2024', '27.00'],
        ['Barbaresco DOCG 2022 Rocche dei 7 Fratelli', '39.00'],
        ["Barbera d'Alba DOC 2022 Lorens", '34.00'],
        ["Barbera d'Alba Ruvei Doc 2022", '27.00'],
        ["Barbera d'Asti Rure' Docg 2023", '25.00'],
        ['Barolo DOCG 2020 Bricco Ambrogio Lorens', '61.00'],
        ['Capoarso Perricone igt 2022', '26.00'],
        ['Chianti Classico d.o.c.g. 2023', '27.00'],
        ['Chianti Classico d.o.c.g. Riserva 2022', '33.00'],
        ['Chianti Classico d.o.c.g. Rocca delle Magie 2023', '83.00'],
        ["Dolcetto d'Alba DOC 2024", '24.00'],
        ["Dolcetto d'Alba Madonna del Dono 2023 Doc", '25.00'],
        ['Finimondo! 2024', '25.00'],
        ['Fontanelle Langhe Barbera 2023', '27.00'],
        ['Junco Frappato igt 2023', '26.00'],
        ['Mandus - Primitivo di Manduria d.o.c. 2023', '25.00'],
        ['Morellino di Scansano d.o.c.g. 2024', '25.00'],
        ["Nebbiolo d'Alba DOC 2024", '29.00'],
        ["Nebbiolo d'Alba Michet Doc 2021", '28.00'],
        ["Negroamaro 'Nterra Puglia igp 2024", '22.00'],
        ['Petra Alto Toscana IGT Sangiovese Rosso 2021', '35.00'],
        ['Petra Hebo Toscana IGT Rosso 2024', '28.00'],
        ['Petra Potenti Toscana IGT Cabernet Sauvignon Rosso 2023', '35.00'],
        ['Pietra Pura - Hyria Primitivo IGP', '23.00'],
        ['Rubizzo Rosso di Toscana I.G.T. 2024', '22.00'],
        ['San Giacomo Langhe Nebbiolo 2023', '29.00'],
        ['Sella&Mosca Dimonios Cannonau di Sardegna Riserva DOC 2022', '28.00'],
        ['Sella&Mosca Medeus Cannonau di Sardegna DOC 2024', '25.00'],
        ['Sella&Mosca Mustazzo Cannonau di Sardegna DOC 2021', '35.00'],
        ["Sella&Mosca Tanca Farra' Alghero Rosso DOC 2022", '31.00'],
        ['Tanaurpi Malbec igt 2023', '26.00'],
        ["Tareni Nero d'Avola doc 2023", '22.00'],
        ['Tareni Syrah igt 2023', '22.00'],
        ['Tripudium Rosso 2022', '31.00'],
        ['Valpolicella DOC Classico Superiore La Fabriseria 2021', '38.00'],
        ["Valpolicella DOC Superiore 2022 Capitel Nicalo'", '25.00'],
        ['Valpolicella DOC Superiore Maternigo 2021', '34.00'],
        ['Valpolicella Ripasso DOC Capitel San Rocco 2022', '30.00'],
        ['Villa Vescovile Merlot - Trentino d.o.c. 2022', '23.00'],
        ['Villa Vescovile Pinot Nero - Trentino d.o.c. 2023', '26.00'],
    ];

    private const VINI_BIANCHI = [
        ['A.A. Chardonnay DOC 2024 Caliz', '26.00'],
        ['A.A. Gewurztraminer DOC 2024 Arenis', '27.00'],
        ['A.A. Gewurztraminer Ris. Brenntal DOC 2023', '38.00'],
        ['A.A. Kerner DOC 2024', '27.00'],
        ['A.A. Muller Thurgau DOC 2024', '25.00'],
        ['A.A. Pinot Bianco DOC 2024', '25.00'],
        ['A.A. Pinot Grigio DOC 2024', '25.00'],
        ['A.A. Sauvignon DOC 2024', '26.00'],
        ['Alba Doc PI CIT', '31.00'],
        ['Langhe DOC Sauvignon Bric Amel 2024', '25.00'],
        ['Bellavista Convento SS Annunciata 2019 Curtefranca DOC Bianco', '48.00'],
        ['Campo Maccione Vermentino i.g.t. Toscana 2024', '25.00'],
        ['Coero Arneis Docg 2023', '27.00'],
        ['Cristina Ascheri Langhe Arneis 2024', '26.00'],
        ['Falanghina i.g.t. Beneventano 2024', '23.00'],
        ['Fiano di Avellino d.o.c.g. 2024', '26.00'],
        ['Friulano Fantin Nodar', '27.00'],
        ['Ga.Ry IGT Veneto Bianco 2023', '30.00'],
        ['Gibele - Zibibbo igt 2024', '26.00'],
        ['Greco di Tufo d.o.c.g. 2024', '26.00'],
        ['Iseis - Zibibbo Pantelleria DOC 2024', '29.00'],
        ['Kelbi - Catarratto igt 2024', '26.00'],
        ['Langhe DOC Chardonnay 2024', '24.00'],
        ['Langhe DOC Chardonnay 2024 Lorens', '37.00'],
        ['Monteleone Anthemis Etna Bianco 2023', '52.00'],
        ['Monteleone Etna Bianco 2024', '37.00'],
        ['Moonlite Chardonnay Toscana i.g.t. 2024', '23.00'],
        ["Olivier Leflaive Aligote' Bourgogne 2022", '39.00'],
        ['Olivier Leflaive Cuvee Margot Bourgogne 2022', '46.00'],
        ['Picolit Fantin Nodar', '50.00'],
        ['Ribolla Gialla Fantin Nodar', '27.00'],
        ['Roero DOCG Arneis 2024', '25.00'],
        ['Sasyri i.g.t. 2023', '25.00'],
        ['Savi del Comune di Gavi Docg 2023', '27.00'],
        ['Savi Docg 2023', '26.00'],
        ['Sella&Mosca Monteoro 2024 Vermentino di Gallura Superiore', '28.00'],
        ['Sella&Mosca Parallelo 41 DOC Alghero Bianco 2024', '31.00'],
        ['Sella&Mosca Terre Bianche 2024 Alghero Torbato', '29.00'],
        ['Senaria Grillo Superiore doc 2023', '26.00'],
        ['Tareni Inzolia igt 2023', '22.00'],
        ['Vernaccia di San Gimignano d.o.c.g. 2024', '23.00'],
        ['Villa Vescovile Gewurztraminer - Trentino d.o.c. 2024', '27.00'],
        ['Villa Vescovile Sauvignon - Trentino d.o.c. 2024', '24.00'],
    ];

    private const BOLLICINE = [
        ['Bellavista Alma Assemblage2 Franciacorta Extra Brut', '43.00'],
        ['Bellavista Alma Non Dosato Franciacorta Dosaggio Zero', '44.00'],
        ["Bellavista Alma Rose' Franciacorta Extra Brut Rose'", '48.00'],
        ['Bellavista Brut Teatro alla Scala 2021 Franciacorta Brut', '50.00'],
        ['Bellavista Nectar Franciacorta Demi Sec', '47.00'],
        ['Bianco-Bianco Charmat Brut', '23.00'],
        ['Cofanetto da 1 Bt Giulio Ferrari 2015', '151.00'],
        ['Contadi Blanc 2020 Franciacorta Extra Brut', '37.00'],
        ['Contadi Brut Franciacorta Brut', '34.00'],
        ['Contadi Pino Nero Riserva 2017 Franciacorta Dosaggio Zero Riserva', '50.00'],
        ['Contadi Saten 2021 Franciacorta Brut Saten', '38.00'],
        ["Ferrari Perle' millesimato 2019", null],
        ["Ferrari Perle' Nero millesimato 2018", '85.00'],
        ['Ferrari Riserva Lunelli millesimato 2016', '74.00'],
        ['Grand Famille - Spumante Metodo Classico Brut', '34.00'],
        ['Lucimare - Spumante Brut Charmat igt', '26.00'],
        ['Magnum Giulio Ferrari Riserva Mill. 2015 - L 1.50 Ca Legno', '286.00'],
        ["Sella&Mosca Oscari' 2022 Alghero Torbato Spumante Brut Metodo Classico", '32.00'],
        ['Valentin Leflaive Sigma20 4.0 Champagne Extra Brut Blanc de Blancs', '61.00'],
        ['Vino Spumante Brut Bianco', '22.00'],
        ['V.S/Valdobb. Pros. Sup. DOCG Rive Dry 2024', '29.00'],
        ['VSAQ/Valdobb. Pros. Sup. DOCG Brut Vedova', '29.00'],
        ['VSAQ/Valdobb. Pros. Sup. DOCG E.Dry Vedova', '29.00'],
    ];
}
