<?php
// templates_db.php
require_once __DIR__ . '/config.php';

$TEMPLATE_JSON = __DIR__ . '/assets/templates/templates.json';

function templates_default_seed() {
    return [
        ['id'=>'birthday','title'=>'Happy Birthday','image'=>'assets/templates/birthday.jpg','premium'=>false],
        ['id'=>'anniversary','title'=>'Happy Anniversary','image'=>'assets/templates/anniversary.jpg','premium'=>false],
        ['id'=>'mother','title'=>'Happy Mother’s Day','image'=>'assets/templates/mother.jpg','premium'=>false],
        ['id'=>'father','title'=>'Happy Father’s Day','image'=>'assets/templates/father.jpg','premium'=>false],
        ['id'=>'eid','title'=>'Happy Eid Mubarak','image'=>'assets/templates/eid.jpg','premium'=>true],
        ['id'=>'wedding','title'=>'Happy Wedding','image'=>'assets/templates/wedding.jpg','premium'=>true],
        ['id'=>'confess','title'=>'Confession Love Letter','image'=>'assets/templates/confess.jpg','premium'=>true],
    ];
}

function templates_load(): array {
    global $TEMPLATE_JSON;

    if (!is_file($TEMPLATE_JSON)) {
        @mkdir(dirname($TEMPLATE_JSON), 0777, true);
        file_put_contents($TEMPLATE_JSON, json_encode(templates_default_seed(), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    }

    $data = json_decode(file_get_contents($TEMPLATE_JSON), true);
    if (!is_array($data)) $data = templates_default_seed();
    return $data;
}

function templates_save(array $templates): bool {
    global $TEMPLATE_JSON;
    @mkdir(dirname($TEMPLATE_JSON), 0777, true);
    return (bool) file_put_contents($TEMPLATE_JSON, json_encode(array_values($templates), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
}

function templates_find(string $id): ?array {
    $templates = templates_load();
    foreach ($templates as $t) if (($t['id'] ?? '') === $id) return $t;
    return null;
}

function templates_upsert(array $item): bool {
    $templates = templates_load();
    $found = false;

    foreach ($templates as $i => $t) {
        if (($t['id'] ?? '') === ($item['id'] ?? '')) {
            $templates[$i] = $item;
            $found = true;
            break;
        }
    }

    if (!$found) $templates[] = $item;
    return templates_save($templates);
}

function templates_delete(string $id): bool {
    $templates = templates_load();
    $templates = array_values(array_filter($templates, fn($t) => ($t['id'] ?? '') !== $id));
    return templates_save($templates);
}
