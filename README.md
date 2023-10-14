# MedShakeST
Aide à l'analyse des effectifs en service interentreprises pour l'équipe santé travail.

## Démonstration
L'application est en production sur https://st.medshake.net/

## Environnement de production nécessaire
- Apache
- PHP 8 ou supérieur
- MariaDB

## Mise en production 
- Cloner le repository
- Executer composer update en racine et dans /public_html
- Créer une base de données dédiée à l'aide du dump base.sql
- Peupler la base avec les données AT/MP/A de trajet disponibles sur https://assurance-maladie.ameli.fr/etudes-et-donnees
- Renommer /config/config.exemple.yml en config.yml et ajuster les paramètres
