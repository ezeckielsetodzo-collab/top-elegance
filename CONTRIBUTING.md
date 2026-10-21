# Règles Git

## Branches

- main : le code en ligne, protégé
- develop : l'assemblage du travail de l'équipe, protégé
- feature/nom-de-la-tache : une branche par tâche, créée depuis develop

## Règles

- Aucun push direct sur main ni sur develop : tout passe par une Pull Request relue par un autre membre.
- Faire git pull sur develop avant de commencer une nouvelle tâche.
- Commits courts et clairs, toujours en français, au format : ajout: ..., correction: ..., modification: ...
- Ne jamais versionner .env, les mots de passe ni le dossier vendor.
- Pull Requests petites, décrites en une phrase.
