# OpenSpec — Vicity

Artefactes SDD i decisions d'aquest projecte. Segueix el flux `sdd-vd` de VoraData.

## Estructura

```
openspec/
├── changes/        ← canvis en curs (<nom-canvi>/proposal.md, spec.md, design.md, tasks.md...)
├── archive/        ← canvis tancats i verificats
└── decisions/      ← decisions del projecte, per stack
    ├── arquitectura/
    └── css/
```

## Mode d'ús

- Mode actiu: **hybrid** (Engram, projecte `vicecity`, + aquests fitxers).
- Cada decisió es desa com a fitxer `decisions/<stack>/<NNN>-<títol>.md` i porta un commit propi.
- Les decisions s'escriuen sempre en català.
- La numeració (`NNN`) és independent per carpeta: `arquitectura/` i `css/` tenen cadascuna la seva pròpia seqüència.
- Format: Estat, Data, Context, Decisió, Conseqüències.
- Quan una decisió n'és substituïda per una altra, s'afegeix just després de `Data` la línia `**Substituïda per**: NNN (motiu en una frase)`. Si només se'n substitueix una part, `**Parcialment substituïda per**: NNN (…)`.
- Els commits són locals. El push només quan l'equip ho indiqui.
