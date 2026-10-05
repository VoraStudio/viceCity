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
- Format: Estat, Data, Context, Decisió, Conseqüències.
- Els commits són locals. El push només quan l'equip ho indiqui.
