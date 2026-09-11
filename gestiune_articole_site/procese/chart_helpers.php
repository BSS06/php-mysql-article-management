<?php
function procese_metric_number($value): float {
    if ($value === null || $value === '') return 0.0;
    return (float)$value;
}
function procese_chart_from_items(string $title, array $items, string $accent = 'violet'): array {
    $max = 0.0;
    foreach ($items as $item) {
        $candidate = abs((float)($item['value'] ?? 0));
        if ($candidate > $max) $max = $candidate;
    }
    if ($max <= 0) $max = 1;
    return ['title' => $title, 'items' => $items, 'max' => $max, 'accent' => $accent];
}
function procese_build_chart(string $taskId, array $ctx): array {
    $scope = $ctx['scope'] ?? [];
    $counts = $scope['counts'] ?? [];
    $a1 = procese_pick_article($ctx, 0);
    $a2 = procese_pick_article($ctx, 1);
    $cat1 = procese_pick_category($ctx, 0);
    $cat2 = procese_pick_category($ctx, 1);
    $n1 = procese_pick_node($ctx, 0);
    $n2 = procese_pick_node($ctx, 1);
    $n3 = procese_pick_node($ctx, 2);
    $u1 = procese_pick_user($ctx, 0);
    $u2 = procese_pick_user($ctx, 1);
    $published = (int)($counts['published_count'] ?? 0);
    $draft = (int)($counts['draft_count'] ?? 0);
    $archived = (int)($counts['archived_count'] ?? 0);
    $comments = (int)($counts['approved_comments'] ?? 0);
    $avgRating = (float)($counts['avg_rating'] ?? 0);
    $activeSessions = (int)($counts['active_sessions'] ?? 0);
    $totalViews = (int)($counts['total_views'] ?? 0);
    switch ($taskId) {
        case 's1_01':
            return procese_chart_from_items('Vizualizări și vechime', [
                ['label' => 'Vizualizări articol 1', 'value' => (int)$a1['numar_vizualizari'], 'display' => (string)(int)$a1['numar_vizualizari']],
                ['label' => 'Vizualizări articol 2', 'value' => (int)$a2['numar_vizualizari'], 'display' => (string)(int)$a2['numar_vizualizari']],
                ['label' => 'Vechime cumulată', 'value' => max(1, (int)((time() - strtotime($a1['data_publicarii'] ?: date('Y-m-d H:i:s'))) / 86400)) + max(1, (int)((time() - strtotime($a2['data_publicarii'] ?: date('Y-m-d H:i:s'))) / 86400)), 'display' => 'zile']
            ]);
        case 's1_02':
            $eng = $totalViews > 0 ? round(($comments / $totalViews) * 100, 2) : 0;
            return procese_chart_from_items('Indicatori fracționari', [
                ['label' => 'Rating mediu', 'value' => $avgRating, 'display' => number_format($avgRating, 2)],
                ['label' => 'Engagement %', 'value' => $eng, 'display' => number_format($eng, 2) . '%'],
                ['label' => 'Comentarii aprobate', 'value' => $comments, 'display' => (string)$comments]
            ], 'green');
        case 's1_03':
            return procese_chart_from_items('Buget operațional', [
                ['label' => 'B1', 'value' => 100 + $published + 0.50, 'display' => (100 + $published) . ',50 lei'],
                ['label' => 'B2', 'value' => 45 + $draft + 0.75, 'display' => (45 + $draft) . ',75 lei'],
                ['label' => 'Publicate', 'value' => $published, 'display' => (string)$published]
            ], 'orange');
        case 's1_04':
            $bazaSus = max(2, $published + 2);
            $bazaJos = max(4, $published + $draft + 4);
            $inaltime = max(2, (int)ceil($comments / 4));
            return procese_chart_from_items('Parametri trapez', [
                ['label' => 'Baza sus', 'value' => $bazaSus, 'display' => (string)$bazaSus],
                ['label' => 'Baza jos', 'value' => $bazaJos, 'display' => (string)$bazaJos],
                ['label' => 'Înălțime', 'value' => $inaltime, 'display' => (string)$inaltime]
            ]);
        case 's1_05':
            return procese_chart_from_items('Triunghi KPI', [
                ['label' => 'Articole publicate', 'value' => $published, 'display' => (string)$published],
                ['label' => 'Comentarii', 'value' => $comments, 'display' => (string)$comments],
                ['label' => 'Rating x10', 'value' => round($avgRating * 10, 2), 'display' => number_format($avgRating * 10, 2)]
            ], 'green');
        case 's1_06':
            return procese_chart_from_items('Operații calculator', [
                ['label' => 'A = articole', 'value' => $published + $draft + $archived, 'display' => (string)($published + $draft + $archived)],
                ['label' => 'B = comentarii', 'value' => $comments, 'display' => (string)$comments],
                ['label' => 'Suma', 'value' => $published + $draft + $archived + $comments, 'display' => (string)($published + $draft + $archived + $comments)]
            ]);
        case 's1_07':
            $hour = ((int)date('H') + 1) % 24;
            $minute = 15;
            return procese_chart_from_items('Programare publicare', [
                ['label' => 'Ora setată', 'value' => $hour, 'display' => sprintf('%02d:00', $hour)],
                ['label' => 'Minut setat', 'value' => $minute, 'display' => sprintf('%02d', $minute)],
                ['label' => 'Sesiuni active', 'value' => $activeSessions, 'display' => (string)$activeSessions]
            ], 'orange');
        case 's1_08':
            $titleLen = strlen((string)$a1['titlu']);
            $catLen = strlen((string)$cat1['nume_categorie']);
            return procese_chart_from_items('Lungimi șiruri', [
                ['label' => 'Titlu', 'value' => $titleLen, 'display' => (string)$titleLen],
                ['label' => 'Categorie', 'value' => $catLen, 'display' => (string)$catLen],
                ['label' => 'Primele 5 caractere', 'value' => strlen(substr((string)$a1['titlu'], 0, 5)), 'display' => substr((string)$a1['titlu'], 0, 5)]
            ]);
        case 's1_11':
            return procese_chart_from_items('Evenimente de acces', [
                ['label' => 'Tentative', 'value' => 2, 'display' => '2'],
                ['label' => 'Modificare parolă', 'value' => 1, 'display' => '1'],
                ['label' => 'Utilizator nou', 'value' => 1, 'display' => '1']
            ], 'orange');
        case 's2_02':
            return procese_chart_from_items('Secțiuni ale portalului', [
                ['label' => (string)$cat1['nume_categorie'], 'value' => (int)$cat1['total_articole'], 'display' => (string)$cat1['total_articole']],
                ['label' => (string)$cat2['nume_categorie'], 'value' => (int)$cat2['total_articole'], 'display' => (string)$cat2['total_articole']],
                ['label' => 'Publicate', 'value' => $published, 'display' => (string)$published]
            ], 'green');
        case 's2_03':
            return procese_chart_from_items('Tabele pe noduri', [
                ['label' => (string)$n1['db_name'], 'value' => (int)$n1['table_count'], 'display' => (string)$n1['table_count']],
                ['label' => (string)$n2['db_name'], 'value' => (int)$n2['table_count'], 'display' => (string)$n2['table_count']],
                ['label' => (string)$n3['db_name'], 'value' => (int)$n3['table_count'], 'display' => (string)$n3['table_count']]
            ]);
        case 's2_05':
            return procese_chart_from_items('Stocare materiale', [
                ['label' => 'Articole', 'value' => count($scope['articles'] ?? []), 'display' => (string)count($scope['articles'] ?? [])],
                ['label' => 'Comentarii', 'value' => $comments, 'display' => (string)$comments],
                ['label' => 'Evaluări', 'value' => (int)($counts['rating_count'] ?? 0), 'display' => (string)(int)($counts['rating_count'] ?? 0)]
            ]);
        case 's2_07':
            return procese_chart_from_items('Profiluri editoriale', [
                ['label' => $u1['nume_complet'] ?: 'Utilizator 1', 'value' => 1000, 'display' => '1000'],
                ['label' => $u2['nume_complet'] ?: 'Utilizator 2', 'value' => 2000, 'display' => '2000'],
                ['label' => 'Suma vârstelor', 'value' => 51, 'display' => '51']
            ], 'green');
        case 's2_08':
            return procese_chart_from_items('Validare atribute', [
                ['label' => 'Vârstă validă', 'value' => 25, 'display' => '25'],
                ['label' => 'Vârstă invalidă', 'value' => 150, 'display' => '150'],
                ['label' => 'Vârstă finală', 'value' => 26, 'display' => '26']
            ], 'orange');
        case 's2_09':
            return procese_chart_from_items('Moștenire utilizatori', [
                ['label' => 'Angajat 1', 'value' => 1000, 'display' => '1000'],
                ['label' => 'Angajat 2', 'value' => 2000, 'display' => '2000'],
                ['label' => 'Bursă student', 'value' => 500, 'display' => '500']
            ]);
        case 's2_11':
            return procese_chart_from_items('Sesiune și flash', [
                ['label' => 'Sesiuni active', 'value' => $activeSessions, 'display' => (string)$activeSessions],
                ['label' => 'Chei setate', 'value' => 2, 'display' => '2'],
                ['label' => 'Mesaj flash', 'value' => 1, 'display' => '1']
            ], 'green');
        default:
            return procese_chart_from_items('Context editorial', [
                ['label' => 'Articole', 'value' => count($scope['articles'] ?? []), 'display' => (string)count($scope['articles'] ?? [])],
                ['label' => 'Comentarii', 'value' => $comments, 'display' => (string)$comments],
                ['label' => 'Rating', 'value' => $avgRating, 'display' => number_format($avgRating, 2)]
            ]);
    }
}
