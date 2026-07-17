<?php
/**
 * Genera la carta degli Stuzzichini: una singola pagina A4 full-bleed,
 * stesso stile grafico di gen-carta-vini.php, ma organizzata per sezioni
 * (Salumi / Formaggi / Tapas / Contorni) con l'elenco delle voci.
 */

$WINE_DIR = 'C:/laragon/www/www.da-andrea-enoteca.it/public/images/graphics_2026_07_15/icons';
$OUT_DIR  = 'C:/laragon/www/www.da-andrea-enoteca.it/public/documents';

$SECTIONS = [
    [
        'name' => 'Salumi',
        'cap'  => 'Tagli italiani e spagnoli, al tagliere.',
        'items' => [
            'Prosciutto Crudo Don Romeo 24 mesi', 'Paleta Ibérico de Cebo', 'Crudo spagnolo',
            'Chorizo', 'Cotto spagnolo', 'Salame Felino', 'Salame al Gorgonzola', 'Bresaola di Fassona',
        ],
    ],
    [
        'name' => 'Formaggi',
        'cap'  => 'Pochi, ma giusti — da accompagnare al calice.',
        'items' => [
            'Pecorino Sardo Sepi', 'Gorgonzola al cucchiaio', 'Grana Padano vacche rosse',
        ],
    ],
    [
        'name' => 'Tapas',
        'cap'  => 'I piccoli morsi per ingannare l’attesa.',
        'items' => [
            'Crostino burro montato e acciuga del Mar Cantabrico',
            'Crostino pomodoro, cipolla, olio e origano',
            'Pancake con Philadelphia, salmone affumicato e gravlax, alghe wakame',
            'Philadelphia, salmone affumicato e crema di avocado',
            'Pancake burro montato e acciuga del Mar Cantabrico',
            'Pancake burro montato e acciuga di Cetara',
            'Bignè salati',
        ],
    ],
    [
        'name' => 'Contorni',
        'cap'  => 'Freschi e di casa, per completare.',
        'items' => [
            'Insalata russa come la fa Antonio', 'Giardiniera CBT', 'Panzanella', 'Coleslaw',
        ],
    ],
];

function renderSections(array $sections): string
{
    $out = '';
    foreach ($sections as $s) {
        $name = htmlspecialchars($s['name'], ENT_QUOTES);
        $cap  = htmlspecialchars($s['cap'], ENT_QUOTES);
        $lis  = '';
        foreach ($s['items'] as $item) {
            $lis .= '<li>' . htmlspecialchars($item, ENT_QUOTES) . "</li>\n";
        }
        $out .= <<<SEC
        <section class="sec">
            <div class="sec-h">
                <h2>{$name}</h2>
                <p class="sec-cap">{$cap}</p>
            </div>
            <ul class="items">
                {$lis}
            </ul>
        </section>

SEC;
    }
    return $out;
}

function pageHtml(array $sections, string $art, string $artClass): string
{
    $secs  = renderSections($sections);
    $title = 'Stuzzichini';
    $sub   = 'Piccoli morsi da condividere';

    return <<<HTML
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Carta degli Stuzzichini — Da Andrea</title>
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
    .page{
        position:relative; width:210mm; height:297mm; overflow:hidden;
        background:radial-gradient(120% 90% at 20% 0%, var(--paper-2) 0%, var(--paper) 55%, #f0dcd7 100%);
        padding:20mm 18mm 14mm;
        display:flex; flex-direction:column;
    }
    .page-art{ position:absolute; z-index:0; opacity:.08; pointer-events:none; }
    .art-grapes{ width:150mm; right:-42mm; bottom:-30mm; transform:rotate(-6deg); }
    .art-glass{ width:120mm; right:-30mm; bottom:-24mm; }

    .head{ position:relative; z-index:1; text-align:center; margin-bottom:9mm; }
    .head .kicker{
        font-family:var(--font-display); font-style:italic; font-size:15px;
        color:var(--ember); letter-spacing:.5px; margin:0 0 4px;
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

    .sections{
        position:relative; z-index:1; flex:1 1 auto;
        column-count:2; column-gap:14mm;
    }
    .sec{ break-inside:avoid; -webkit-column-break-inside:avoid; margin-bottom:8mm; }
    .sec-h{ margin-bottom:4px; }
    .sec-h h2{
        font-family:var(--font-display); font-weight:700; font-size:24px;
        color:var(--wine); margin:0; letter-spacing:.3px;
    }
    .sec-cap{
        font-style:italic; font-size:10px; color:var(--wine-2); margin:2px 0 0;
    }
    ul.items{ list-style:none; margin:6px 0 0; padding:0; }
    ul.items li{
        break-inside:avoid; padding:4px 0;
        border-bottom:1px dotted rgba(92,29,36,.16);
        font-weight:400; color:var(--ink);
    }

    .foot{
        position:relative; z-index:1; margin-top:7mm; padding-top:6px;
        border-top:1px solid var(--line); text-align:center;
        font-size:9px; color:var(--muted); letter-spacing:.4px;
    }
</style>
</head>
<body>
    <div class="page">
        <div class="page-art {$artClass}">{$art}</div>
        <div class="head">
            <p class="kicker">Da Andrea · Cucina con Enoteca</p>
            <h1>{$title}</h1>
            <div class="sub">{$sub}</div>
            <div class="rule"></div>
        </div>
        <div class="sections">
            {$secs}
        </div>
        <div class="foot">
            <span>Via Inveruno 30 · Casorezzo (MI) · 351 859 5639</span>
        </div>
    </div>
</body>
</html>
HTML;
}

$glass = file_get_contents($WINE_DIR . '/floating-glass.svg');

@mkdir($OUT_DIR, 0777, true);
$path = $OUT_DIR . '/carta-stuzzichini.html';
file_put_contents($path, pageHtml($SECTIONS, $glass, 'art-glass'));
echo "HTML: {$path}\n";
echo "Fatto.\n";
