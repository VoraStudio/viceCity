<?php

declare(strict_types=1);

// Entrades del blog. Per afegir-ne o editar-ne, es modifica aquest fitxer i es refresca:
// la BD (data/blog.sqlite) es sincronitza sola per slug (vegeu includes/db.php).
//
// Camps: slug (únic, minúscules i guions), title, category (slug de includes/categories.php),
// excerpt, body (text pla; paràgrafs separats per una línia en blanc), cover (lav | purple | ink),
// read_minutes, published_at (YYYY-MM-DD) i is_featured (només cal a la destacada).
return [
    [
        'slug' => 'grans-proveidors-generalistes-ajuntament-petit',
        'title' => 'Per què els grans proveïdors generalistes no encaixen amb un ajuntament petit o mitjà',
        'category' => 'integracio',
        'excerpt' => "Una mirada al que diferencia una eina pensada des de dins de l'administració local d'un ERP genèric adaptat a cop de mòdul.",
        'body' => implode("\n\n", [
            "Un ERP generalista està pensat per cobrir moltes indústries alhora. Això el fa potent, però també implica que la gestió tributària local hi acaba arribant com un mòdul més, adaptat sobre un model de dades que no va néixer per a padrons, liquidacions i recaptació en voluntària i executiva.",
            "Un ajuntament petit o mitjà sol tenir equips reduïts, on una mateixa persona porta diversos tributs. Necessita eines que parlin el seu llenguatge i que es puguin posar en marxa sense un projecte llarg de parametrització ni consultors externs durant mesos.",
            "Una eina pensada des de dins de l'administració local parteix dels procediments reals: de com es notifica, de com s'embarga i de com es rendeixen comptes. Aquesta coherència redueix l'adaptació a mida i, amb ella, el cost de mantenir la solució al llarg dels anys.",
        ]),
        'cover' => 'lav',
        'read_minutes' => 7,
        'published_at' => '2026-09-18',
        'is_featured' => 1,
    ],
    [
        'slug' => 'ia-detectar-expedients-estancats',
        'title' => 'Com la IA pot detectar expedients estancats abans que sigui tard',
        'category' => 'ia',
        'excerpt' => 'Tres casos pràctics de com el llenguatge natural agilita la revisió d\'expedients tributaris.',
        'body' => implode("\n\n", [
            "Un expedient estancat rarament fa soroll: simplement deixa de moure's. Quan es detecta, sovint ja s'ha acostat un termini o s'ha perdut una oportunitat de cobrament.",
            "La IA pot ajudar a revisar de manera contínua l'estat dels expedients i a assenyalar-ne els que porten massa temps sense activitat o als quals els falta un tràmit previst. L'avantatge del llenguatge natural és que el tècnic pot preguntar directament, sense construir filtres ni informes a mida.",
            "Cal tenir clar que la IA assenyala i prioritza, però la decisió continua sent de la persona responsable de l'expedient. Per això és important que cada avís es pugui revisar i entendre.",
        ]),
        'cover' => 'lav',
        'read_minutes' => 5,
        'published_at' => '2026-09-12',
    ],
    [
        'slug' => 'indicadors-interventor-recaptacio',
        'title' => '5 indicadors que qualsevol interventor hauria de seguir cada mes',
        'category' => 'recaptacio',
        'excerpt' => 'Una guia ràpida per prioritzar el control i seguiment de la recaptació municipal.',
        'body' => implode("\n\n", [
            "El control de la recaptació no necessita desenes de gràfics. Amb uns quants indicadors ben escollits i revisats amb regularitat n'hi ha prou per veure cap on va l'exercici i reaccionar a temps.",
            "Entre els més útils hi ha el ritme de cobrament respecte al previst, l'evolució del pendent en voluntària, el volum d'expedients en executiva, l'antiguitat del deute i el percentatge de rebuts domiciliats.",
            "El més important no és el valor puntual de cada indicador, sinó la seva tendència mes a mes. Fixar un moment de revisió fix al calendari ajuda que el seguiment no depengui de la memòria de ningú.",
        ]),
        'cover' => 'purple',
        'read_minutes' => 4,
        'published_at' => '2026-09-03',
    ],
    [
        'slug' => 'interoperabilitat-sense-reescriure-res',
        'title' => 'Interoperabilitat sense reescriure res',
        'category' => 'integracio',
        'excerpt' => "El cas d'un consell comarcal que va integrar Vicity sense tocar els seus sistemes existents.",
        'body' => implode("\n\n", [
            "Integrar un sistema nou no vol dir substituir els que ja funcionen. Moltes administracions tenen aplicacions de comptabilitat, registre o gestió documental que són vàlides i que no té sentit reescriure.",
            "L'enfocament passa per connectar-s'hi mitjançant interfícies i intercanvis de dades ben definits, de manera que cada sistema conservi la seva funció i la informació es mogui entre ells sense duplicar-la a mà.",
            "Aquest mètode redueix el risc del projecte: el canvi és gradual, es pot validar pas a pas i, si cal, es pot revertir sense afectar el servei als ciutadans.",
        ]),
        'cover' => 'ink',
        'read_minutes' => 6,
        'published_at' => '2026-08-28',
    ],
    [
        'slug' => 'del-paper-al-tauler-digitalitzacio-ajuntament',
        'title' => "Del paper al tauler: la digitalització d'un ajuntament de 8.000 habitants",
        'category' => 'casos-us',
        'excerpt' => 'Com van reduir a la meitat el temps de gestió de rebuts en tres mesos.',
        'body' => implode("\n\n", [
            "Aquest cas parteix d'un ajuntament mitjà on bona part de la gestió de rebuts es feia amb paper, fulls de càlcul i tasques repetides entre diferents persones de l'equip.",
            "El primer pas va ser ordenar el circuit: qui fa què, en quin moment i amb quina informació. Només després es va portar aquest circuit a una eina única, amb un tauler que mostra l'estat de cada gestió d'un cop d'ull.",
            "El resultat més visible per a l'equip va ser guanyar temps per a tasques de més valor, com l'atenció als contribuents, en comptes de dedicar-lo a copiar i comprovar dades.",
        ]),
        'cover' => 'lav',
        'read_minutes' => 5,
        'published_at' => '2026-08-14',
    ],
    [
        'slug' => 'calendari-fiscal-2026-2027',
        'title' => 'Calendari fiscal 2026-2027: dates clau per a la gestió tributària local',
        'category' => 'recaptacio',
        'excerpt' => "Un resum de terminis i finestres de recaptació que cap tècnic hauria de perdre's.",
        'body' => implode("\n\n", [
            "El calendari fiscal organitza la feina de tot l'any: quan s'aproven els padrons, quan s'obren i es tanquen els períodes de cobrament i quan s'inicien les actuacions en executiva.",
            "Aquest article és una guia d'orientació per planificar, no una referència normativa. Les dates concretes de cada tribut les fixa cada ajuntament a la seva ordenança i al seu calendari del contribuent, i cal consultar-les allà.",
            "Una bona pràctica és traslladar aquestes dates a un calendari compartit amb marge d'antelació, de manera que els terminis interns d'un departament no se superposin amb els d'un altre.",
        ]),
        'cover' => 'purple',
        'read_minutes' => 4,
        'published_at' => '2026-08-02',
    ],
    [
        'slug' => 'preguntar-en-llenguatge-natural-informes',
        'title' => 'Preguntar en llenguatge natural: el nou estàndard dels informes municipals',
        'category' => 'ia',
        'excerpt' => 'Per què cada cop més tècnics prefereixen preguntar abans que filtrar taules.',
        'body' => implode("\n\n", [
            "Tradicionalment, obtenir una dada d'un informe volia dir saber on era, aplicar els filtres adequats i exportar la taula. Si la pregunta canviava una mica, calia tornar a començar.",
            "Amb el llenguatge natural, el tècnic formula la pregunta tal com la pensa, per exemple quins rebuts continuen pendents en un barri o en un exercici, i obté la resposta sobre les dades reals de l'ajuntament.",
            "Perquè sigui fiable, la resposta ha d'indicar sempre d'on surt la dada i permetre'n la comprovació. La confiança en l'eina depèn d'aquesta traçabilitat.",
        ]),
        'cover' => 'ink',
        'read_minutes' => 6,
        'published_at' => '2026-07-20',
    ],
        [
        'slug' => 'entrada-de-prova-al-blog',
        'title' => 'Entrada de prova al blog',
        'category' => 'ia',
        'excerpt' => 'Per què cada cop més tècnics prefereixen preguntar abans que filtrar taules.',
        'body' => implode("\n\n", [
            "Tradicionalment, obtenir una dada d'un informe volia dir saber on era, aplicar els filtres adequats i exportar la taula. Si la pregunta canviava una mica, calia tornar a començar.",
            "Amb el llenguatge natural, el tècnic formula la pregunta tal com la pensa, per exemple quins rebuts continuen pendents en un barri o en un exercici, i obté la resposta sobre les dades reals de l'ajuntament.",
            "Perquè sigui fiable, la resposta ha d'indicar sempre d'on surt la dada i permetre'n la comprovació. La confiança en l'eina depèn d'aquesta traçabilitat.",
        ]),
        'cover' => 'ink',
        'read_minutes' => 6,
        'published_at' => '2026-07-20',
    ],
];
