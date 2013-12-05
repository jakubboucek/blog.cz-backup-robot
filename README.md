blog.cz backup robot
====================
[Czech description only]

Česká blogovací služba blog.cz „vyniká“ tím, že neumožňuje uživatelům vyexportovat a zálohovat jejich vlastní data. I když se schovávají za různé výmluvy, není pochyb, že hlavním motivem odpírání této služby jsou obavy z odchodu uživatelů do jiných sítí. Blog.cz mezi konkurencí disponuje výhodou v podobě poměrně silné komunity blogerů, kteří jsou stále velmi aktivní a přinášejí tak provozovateli nemalé příjmy.

Nicméně služba je postupem času více nespolehlivější a místo oprav a vylepšení se ukazuje, že admini služby pracují předevním na zvýšení zvých tržeb různými postraními aktivitami (nabízení ICQ, obsazování blogů agresivními reklamami, ap.).

O aplikaci
----------

Tato aplikace je *hrubý funkční koncept*, který v několika krocích vyhledá, stáhne a vyparsuje všechny články a připraví náhled v podobě statických HTML souborů. Všechna získaná data si dále udržuje v datových strukturách (JSON) pro další zpracování.

Zpracování je fázové, nastavení je zatím pouze v souboru `include.php`, spouští se z příkazové řádky jednoduše voláním ve tvaru `php 05-create-static-pages.php` s tím, že jednotlivé kroky jsou očíslové a velmi úzce na sebe navazují, ktedy potřeba je spouštět sekvenčně za sebou.

Aplikace vyžaduje pouze PHP (verze alespoň 5.4), používá XML DOM knihovnu a JSON. Nepotřebuje databáze a není radno ji spouštět přes webový server (stahování může bězet skoro hodinu).

Alpikaci jsem účelově napsal během několika hodin a podle toho to vypadá. Kritiku nepřijímám, pull-requesty ano :)
