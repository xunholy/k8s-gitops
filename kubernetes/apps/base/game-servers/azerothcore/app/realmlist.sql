-- Realm identity for the auth DB. Edited inline by humans; consumed by
-- realm-config-job.yaml via a hash-suffixed ConfigMap. Changes here
-- trigger automatic Job re-runs (via the kustomize.toolkit.fluxcd.io/force
-- annotation on the Job).
UPDATE realmlist
SET name = 'Emberstone',
    address = '${EXTERNAL_IP}',
    port = 3726,
    gamebuild = 12340
WHERE id = 1;
SELECT id, name, address, port, gamebuild FROM realmlist WHERE id = 1;
