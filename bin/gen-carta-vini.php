<?php
/**
 * Genera la carta dei vini: 3 file HTML distinti (bianchi / rossi / bollicine),
 * ognuno una singola pagina A4 full-bleed (senza bordi bianchi) con titolo grande.
 * Riusa la stessa logica di App\Command\SeedWinesCommand.
 */

$WINE_DIR = 'C:/laragon/www/www.da-andrea-enoteca.it/public/images/wine';
$OUT_DIR  = 'C:/laragon/www/www.da-andrea-enoteca.it/public/documents';

$VINI_ROSSI = [
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

$VINI_BIANCHI = [
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

$BOLLICINE = [
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

function extractYear(string $name): ?int
{
    if (preg_match_all('/\b(?:19|20)\d{2}\b/', $name, $m) && $m[0]) {
        return (int) end($m[0]);
    }
    return null;
}

function displayName(string $name, ?int $year): string
{
    if ($year === null) {
        return $name;
    }
    $n = preg_replace('/\b' . $year . '\b/', '', $name);
    $n = preg_replace('/\s{2,}/', ' ', (string) $n);
    $n = trim((string) $n);
    $n = trim($n, " -\u{2013}");
    return trim($n);
}

function renderList(array $names): string
{
    $items = [];
    foreach ($names as $name) {
        $year = extractYear($name);
        $items[] = ['dn' => displayName($name, $year), 'year' => $year];
    }
    usort($items, fn($a, $b) => strcasecmp($a['dn'], $b['dn']));

    $rows = '';
    foreach ($items as $it) {
        $dn = htmlspecialchars($it['dn'], ENT_QUOTES);
        $yr = $it['year'] !== null
            ? '<span class="yr">' . $it['year'] . '</span>'
            : '<span class="yr yr-nv">s.a.</span>';
        $rows .= "<li><span class=\"nm\">{$dn}</span><span class=\"dot\"></span>{$yr}</li>\n";
    }
    return $rows;
}

function pageHtml(array $s): string
{
    $count  = count($s['names']);
    $cols   = $count > 26 ? 2 : 1;   // liste lunghe su 2 colonne, corte su 1
    $rows   = renderList($s['names']);
    $title  = htmlspecialchars($s['title']);
    $sub    = htmlspecialchars($s['sub']);
    $num    = $s['n'];
    $artCls = $s['artClass'];
    $art    = $s['art'];

    return <<<HTML
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Carta dei Vini — {$title} — Da Andrea</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,800;1,500;1,600&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
    :root{
        --wine:#5C1D24; --wine-2:#7d2731; --ember:#D95D39;
        --ink:#40161b; --muted:rgba(64,22,27,.55);
        --paper:#f6e6e2; --paper-2:#fbf1ee;
        --line:rgba(92,29,36,.18);
        --font-display:'Playfair Display', Georgia, serif;
        --font-sans:'Poppins', system-ui, sans-serif;
    }
    @page{ size:A4; margin:0; }
    *{ box-sizing:border-box; }
    html,body{ margin:0; padding:0; }
    body{
        font-family:var(--font-sans); color:var(--ink);
        -webkit-print-color-adjust:exact; print-color-adjust:exact;
        font-size:10px; line-height:1.4;
    }
    /* pagina full-bleed: lo sfondo arriva ai bordi, niente margini bianchi */
    .page{
        position:relative; width:210mm; height:297mm; overflow:hidden;
        background:radial-gradient(120% 90% at 20% 0%, var(--paper-2) 0%, var(--paper) 55%, #f0dcd7 100%);
        padding:20mm 18mm 14mm;
        display:flex; flex-direction:column;
    }
    /* illustrazione SVG che sborda dal bordo (effetto full-bleed) */
    .page-art{ position:absolute; z-index:0; opacity:.08; pointer-events:none; }
    .art-grapes{ width:150mm; right:-42mm; bottom:-30mm; transform:rotate(-6deg); }
    .art-glass{ width:120mm; right:-30mm; bottom:-24mm; }

    .head{ position:relative; z-index:1; text-align:center; margin-bottom:9mm; }
    .head .kicker{
        font-family:var(--font-display); font-style:italic; font-size:15px;
        color:var(--ember); letter-spacing:.5px; margin:0 0 4px;
    }
    .head .num{
        display:inline-flex; align-items:center; justify-content:center;
        width:30px; height:30px; border-radius:50%; border:1.5px solid var(--wine);
        font-family:var(--font-display); font-weight:700; font-size:16px; color:var(--wine);
        margin-bottom:8px;
    }
    .head h1{
        font-family:var(--font-display); font-weight:800;
        font-size:68px; line-height:.95; letter-spacing:.5px;
        color:var(--wine); margin:0;
    }
    .head .sub{
        font-family:var(--font-display); font-style:italic; font-size:17px;
        color:var(--wine-2); letter-spacing:1px; margin:6px 0 0;
    }
    .head .rule{ width:52mm; height:1px; background:var(--line); margin:12px auto 0; position:relative; }
    .head .rule::before{
        content:""; position:absolute; left:50%; top:50%; width:6px; height:6px;
        border-radius:50%; background:var(--ember); transform:translate(-50%,-50%);
    }

    ul.wines{
        position:relative; z-index:1; list-style:none; margin:0; padding:0;
        column-count:{$cols}; column-gap:14mm; flex:1 1 auto;
    }
    ul.wines li{
        display:flex; align-items:baseline; break-inside:avoid;
        -webkit-column-break-inside:avoid; padding:5px 0;
        border-bottom:1px dotted rgba(92,29,36,.16);
    }
    ul.wines .nm{ font-weight:400; color:var(--ink); padding-right:6px; }
    ul.wines .dot{ flex:1 1 auto; }
    ul.wines .yr{
        font-family:var(--font-display); font-weight:600; font-style:italic;
        color:var(--wine-2); white-space:nowrap; padding-left:6px;
    }
    ul.wines .yr-nv{ color:var(--muted); font-size:9px; }

    .foot{
        position:relative; z-index:1; margin-top:7mm; padding-top:6px;
        border-top:1px solid var(--line); text-align:center;
        font-size:9px; color:var(--muted); letter-spacing:.4px;
    }
</style>
</head>
<body>
    <div class="page">
        <div class="page-art {$artCls}">{$art}</div>
        <div class="head">
            <p class="kicker">Da Andrea · Cucina con Enoteca</p>
            <h1>{$title}</h1>
            <div class="sub">{$sub}</div>
            <div class="rule"></div>
        </div>
        <ul class="wines">
            {$rows}
        </ul>
        <div class="foot">
            <span>Via Inveruno 30 · Casorezzo (MI) · 351 859 5639</span>
        </div>
    </div>
</body>
</html>
HTML;
}

$grapes = file_get_contents($WINE_DIR . '/floating-grapes.svg');
$glass  = file_get_contents($WINE_DIR . '/floating-glass.svg');

$sections = [
    ['n' => 1, 'file' => 'carta-bianchi',   'title' => 'Vini Bianchi', 'sub' => 'White Wines', 'names' => $VINI_BIANCHI, 'art' => $glass,  'artClass' => 'art-glass'],
    ['n' => 2, 'file' => 'carta-rossi',     'title' => 'Vini Rossi',   'sub' => 'Red Wines',   'names' => $VINI_ROSSI,   'art' => $grapes, 'artClass' => 'art-grapes'],
    ['n' => 3, 'file' => 'carta-bollicine', 'title' => 'Bollicine',    'sub' => 'Sparkling',   'names' => $BOLLICINE,    'art' => $grapes, 'artClass' => 'art-grapes'],
];

@mkdir($OUT_DIR, 0777, true);
foreach ($sections as $s) {
    $path = $OUT_DIR . '/' . $s['file'] . '.html';
    file_put_contents($path, pageHtml($s));
    echo "HTML: {$path}  (" . count($s['names']) . " vini)\n";
}
echo "Fatto.\n";
