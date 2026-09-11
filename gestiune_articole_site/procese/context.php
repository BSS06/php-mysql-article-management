<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config.php';
function procese_q_first(string $sql): array {
    $rows = q(DB1, $sql);
    return $rows[0] ?? [];
}
function procese_context(array $user): array {
    $uid = (int)($user['id'] ?? 0);
    $role = (string)($user['rol'] ?? 'cititor');
    $isAdmin = ($role === 'administrator');
    $isEditor = ($role === 'redactor');
    $isReader = ($role === 'cititor');
    $users = q(DB1, "SELECT u.id_utilizator, CONCAT(u.prenume, ' ', u.nume) AS nume_complet, u.email, u.activ, r.denumire_rol
        FROM utilizatori u
        JOIN roluri r ON r.id_rol = u.id_rol
        ORDER BY u.id_utilizator ASC
        LIMIT 12");
    $editors = q(DB1, "SELECT u.id_utilizator, CONCAT(u.prenume, ' ', u.nume) AS nume_complet, u.email, u.activ
        FROM utilizatori u
        WHERE u.id_rol = 2
        ORDER BY u.id_utilizator ASC
        LIMIT 12");
    $roleCounts = q(DB1, "SELECT r.denumire_rol, COUNT(u.id_utilizator) AS total
        FROM roluri r
        LEFT JOIN utilizatori u ON u.id_rol = r.id_rol
        GROUP BY r.id_rol
        ORDER BY r.id_rol ASC");
    $articles = q(DB1, "SELECT a.id_articol, a.titlu, a.status, a.numar_vizualizari, a.data_publicarii,
            c.nume_categorie,
            CONCAT(u.prenume, ' ', u.nume) AS autor
        FROM " . DB2 . ".articole a
        LEFT JOIN " . DB2 . ".categorii c ON c.id_categorie = a.id_categorie
        LEFT JOIN " . DB1 . ".utilizatori u ON u.id_utilizator = a.id_autor
        ORDER BY a.id_articol ASC
        LIMIT 24");
    $publicArticles = q(DB1, "SELECT a.id_articol, a.titlu, a.status, a.numar_vizualizari, a.data_publicarii,
            c.nume_categorie,
            CONCAT(u.prenume, ' ', u.nume) AS autor
        FROM " . DB2 . ".articole a
        LEFT JOIN " . DB2 . ".categorii c ON c.id_categorie = a.id_categorie
        LEFT JOIN " . DB1 . ".utilizatori u ON u.id_utilizator = a.id_autor
        WHERE a.status = 'publicat'
        ORDER BY a.id_articol ASC
        LIMIT 24");
    $ownArticles = q(DB1, "SELECT a.id_articol, a.titlu, a.status, a.numar_vizualizari, a.data_publicarii,
            c.nume_categorie,
            CONCAT(u.prenume, ' ', u.nume) AS autor
        FROM " . DB2 . ".articole a
        LEFT JOIN " . DB2 . ".categorii c ON c.id_categorie = a.id_categorie
        LEFT JOIN " . DB1 . ".utilizatori u ON u.id_utilizator = a.id_autor
        WHERE a.id_autor = {$uid}
        ORDER BY a.id_articol ASC
        LIMIT 24");
    $categories = q(DB1, "SELECT c.id_categorie, c.nume_categorie, COUNT(a.id_articol) AS total_articole
        FROM " . DB2 . ".categorii c
        LEFT JOIN " . DB2 . ".articole a ON a.id_categorie = c.id_categorie
        GROUP BY c.id_categorie
        ORDER BY c.id_categorie ASC");
    $countsGlobal = procese_q_first("SELECT
        (SELECT COUNT(*) FROM " . DB2 . ".articole WHERE status = 'publicat') AS published_count,
        (SELECT COUNT(*) FROM " . DB2 . ".articole WHERE status = 'draft') AS draft_count,
        (SELECT COUNT(*) FROM " . DB2 . ".articole WHERE status = 'arhivat') AS archived_count,
        (SELECT COUNT(*) FROM " . DB3 . ".comentarii WHERE aprobat = 1) AS approved_comments,
        (SELECT ROUND(AVG(nota), 2) FROM " . DB3 . ".evaluari) AS avg_rating,
        (SELECT COUNT(*) FROM " . DB1 . ".sesiuni WHERE activa = 1) AS active_sessions,
        (SELECT COUNT(*) FROM " . DB3 . ".evaluari) AS rating_count,
        (SELECT COUNT(*) FROM " . DB3 . ".vizualizari) AS total_views");
    $countsOwn = procese_q_first("SELECT
        (SELECT COUNT(*) FROM " . DB2 . ".articole WHERE id_autor = {$uid} AND status = 'publicat') AS published_count,
        (SELECT COUNT(*) FROM " . DB2 . ".articole WHERE id_autor = {$uid} AND status = 'draft') AS draft_count,
        (SELECT COUNT(*) FROM " . DB2 . ".articole WHERE id_autor = {$uid} AND status = 'arhivat') AS archived_count,
        (SELECT COUNT(*) FROM " . DB3 . ".comentarii c JOIN " . DB2 . ".articole a ON a.id_articol = c.id_articol WHERE a.id_autor = {$uid} AND c.aprobat = 1) AS approved_comments,
        (SELECT ROUND(AVG(e.nota), 2) FROM " . DB3 . ".evaluari e JOIN " . DB2 . ".articole a ON a.id_articol = e.id_articol WHERE a.id_autor = {$uid}) AS avg_rating,
        (SELECT COUNT(*) FROM " . DB1 . ".sesiuni WHERE id_utilizator = {$uid} AND activa = 1) AS active_sessions,
        (SELECT COUNT(*) FROM " . DB3 . ".evaluari e JOIN " . DB2 . ".articole a ON a.id_articol = e.id_articol WHERE a.id_autor = {$uid}) AS rating_count,
        (SELECT COUNT(*) FROM " . DB3 . ".vizualizari v JOIN " . DB2 . ".articole a ON a.id_articol = v.id_articol WHERE a.id_autor = {$uid}) AS total_views");
    $countsPublic = procese_q_first("SELECT
        (SELECT COUNT(*) FROM " . DB2 . ".articole WHERE status = 'publicat') AS published_count,
        0 AS draft_count,
        0 AS archived_count,
        (SELECT COUNT(*) FROM " . DB3 . ".comentarii c JOIN " . DB2 . ".articole a ON a.id_articol = c.id_articol WHERE a.status = 'publicat' AND c.aprobat = 1) AS approved_comments,
        (SELECT ROUND(AVG(e.nota), 2) FROM " . DB3 . ".evaluari e JOIN " . DB2 . ".articole a ON a.id_articol = e.id_articol WHERE a.status = 'publicat') AS avg_rating,
        (SELECT COUNT(*) FROM " . DB1 . ".sesiuni WHERE id_utilizator = {$uid} AND activa = 1) AS active_sessions,
        (SELECT COUNT(*) FROM " . DB3 . ".evaluari e JOIN " . DB2 . ".articole a ON a.id_articol = e.id_articol WHERE a.status = 'publicat') AS rating_count,
        (SELECT COUNT(*) FROM " . DB3 . ".vizualizari v JOIN " . DB2 . ".articole a ON a.id_articol = v.id_articol WHERE a.status = 'publicat') AS total_views");
    $topArticleGlobal = procese_q_first("SELECT a.id_articol, a.titlu, a.status, a.numar_vizualizari, a.data_publicarii,
            c.nume_categorie,
            CONCAT(u.prenume, ' ', u.nume) AS autor
        FROM " . DB2 . ".articole a
        LEFT JOIN " . DB2 . ".categorii c ON c.id_categorie = a.id_categorie
        LEFT JOIN " . DB1 . ".utilizatori u ON u.id_utilizator = a.id_autor
        ORDER BY a.numar_vizualizari DESC, a.id_articol ASC
        LIMIT 1");
    $topArticleOwn = procese_q_first("SELECT a.id_articol, a.titlu, a.status, a.numar_vizualizari, a.data_publicarii,
            c.nume_categorie,
            CONCAT(u.prenume, ' ', u.nume) AS autor
        FROM " . DB2 . ".articole a
        LEFT JOIN " . DB2 . ".categorii c ON c.id_categorie = a.id_categorie
        LEFT JOIN " . DB1 . ".utilizatori u ON u.id_utilizator = a.id_autor
        WHERE a.id_autor = {$uid}
        ORDER BY a.numar_vizualizari DESC, a.id_articol ASC
        LIMIT 1");
    $topArticlePublic = procese_q_first("SELECT a.id_articol, a.titlu, a.status, a.numar_vizualizari, a.data_publicarii,
            c.nume_categorie,
            CONCAT(u.prenume, ' ', u.nume) AS autor
        FROM " . DB2 . ".articole a
        LEFT JOIN " . DB2 . ".categorii c ON c.id_categorie = a.id_categorie
        LEFT JOIN " . DB1 . ".utilizatori u ON u.id_utilizator = a.id_autor
        WHERE a.status = 'publicat'
        ORDER BY a.numar_vizualizari DESC, a.id_articol ASC
        LIMIT 1");
    $latestArticleGlobal = procese_q_first("SELECT a.id_articol, a.titlu, a.status, a.numar_vizualizari, a.data_publicarii,
            c.nume_categorie,
            CONCAT(u.prenume, ' ', u.nume) AS autor
        FROM " . DB2 . ".articole a
        LEFT JOIN " . DB2 . ".categorii c ON c.id_categorie = a.id_categorie
        LEFT JOIN " . DB1 . ".utilizatori u ON u.id_utilizator = a.id_autor
        ORDER BY a.id_articol DESC
        LIMIT 1");
    $latestArticleOwn = procese_q_first("SELECT a.id_articol, a.titlu, a.status, a.numar_vizualizari, a.data_publicarii,
            c.nume_categorie,
            CONCAT(u.prenume, ' ', u.nume) AS autor
        FROM " . DB2 . ".articole a
        LEFT JOIN " . DB2 . ".categorii c ON c.id_categorie = a.id_categorie
        LEFT JOIN " . DB1 . ".utilizatori u ON u.id_utilizator = a.id_autor
        WHERE a.id_autor = {$uid}
        ORDER BY a.id_articol DESC
        LIMIT 1");
    $latestArticlePublic = procese_q_first("SELECT a.id_articol, a.titlu, a.status, a.numar_vizualizari, a.data_publicarii,
            c.nume_categorie,
            CONCAT(u.prenume, ' ', u.nume) AS autor
        FROM " . DB2 . ".articole a
        LEFT JOIN " . DB2 . ".categorii c ON c.id_categorie = a.id_categorie
        LEFT JOIN " . DB1 . ".utilizatori u ON u.id_utilizator = a.id_autor
        WHERE a.status = 'publicat'
        ORDER BY a.id_articol DESC
        LIMIT 1");
    $nodes = q(DB1, "SELECT TABLE_SCHEMA AS db_name, COUNT(*) AS table_count
        FROM INFORMATION_SCHEMA.TABLES
        WHERE TABLE_SCHEMA IN ('" . DB1 . "', '" . DB2 . "', '" . DB3 . "')
          AND TABLE_TYPE = 'BASE TABLE'
        GROUP BY TABLE_SCHEMA
        ORDER BY TABLE_SCHEMA ASC");
    if ($isAdmin) {
        $scopeArticles = $articles;
        $scopeCounts = $countsGlobal;
        $scopeTop = $topArticleGlobal;
        $scopeLatest = $latestArticleGlobal;
        $scopeLabel = 'global';
    } elseif ($isEditor) {
        $scopeArticles = !empty($ownArticles) ? $ownArticles : $articles;
        $scopeCounts = $countsOwn;
        $scopeTop = !empty($topArticleOwn) ? $topArticleOwn : $topArticleGlobal;
        $scopeLatest = !empty($latestArticleOwn) ? $latestArticleOwn : $latestArticleGlobal;
        $scopeLabel = 'articole proprii';
    } else {
        $scopeArticles = $publicArticles;
        $scopeCounts = $countsPublic;
        $scopeTop = $topArticlePublic;
        $scopeLatest = $latestArticlePublic;
        $scopeLabel = 'conținut public';
    }
    return [
        'user' => $user,
        'current_role' => $role,
        'is_admin' => $isAdmin,
        'is_editor' => $isEditor,
        'is_reader' => $isReader,
        'users' => $users,
        'editors' => $editors,
        'roles' => $roleCounts,
        'articles' => $articles,
        'public_articles' => $publicArticles,
        'own_articles' => $ownArticles,
        'categories' => $categories,
        'nodes' => $nodes,
        'global_counts' => $countsGlobal,
        'own_counts' => $countsOwn,
        'public_counts' => $countsPublic,
        'global_top_article' => $topArticleGlobal,
        'own_top_article' => $topArticleOwn,
        'public_top_article' => $topArticlePublic,
        'global_latest_article' => $latestArticleGlobal,
        'own_latest_article' => $latestArticleOwn,
        'public_latest_article' => $latestArticlePublic,
        'scope' => [
            'label' => $scopeLabel,
            'articles' => $scopeArticles,
            'counts' => $scopeCounts,
            'top_article' => $scopeTop,
            'latest_article' => $scopeLatest,
        ],
    ];
}
function procese_pick_article(array $ctx, int $index = 0): array {
    $articles = $ctx['scope']['articles'] ?? [];
    if (isset($articles[$index])) return $articles[$index];
    if (!empty($articles)) return $articles[0];
    return [
        'id_articol' => $index + 1,
        'titlu' => 'Articol demo ' . ($index + 1),
        'status' => 'draft',
        'numar_vizualizari' => 0,
        'data_publicarii' => date('Y-m-d H:i:s'),
        'nume_categorie' => 'General',
        'autor' => $ctx['user']['nume'] ?? 'Utilizator portal',
    ];
}
function procese_pick_user(array $ctx, int $index = 0): array {
    $users = $ctx['users'] ?? [];
    if (isset($users[$index])) return $users[$index];
    if (!empty($users)) return $users[0];
    return [
        'id_utilizator' => $index + 1,
        'nume_complet' => 'Utilizator demo ' . ($index + 1),
        'email' => 'demo' . ($index + 1) . '@portal.md',
        'activ' => 1,
        'denumire_rol' => 'redactor',
    ];
}
function procese_pick_editor(array $ctx, int $index = 0): array {
    $editors = $ctx['editors'] ?? [];
    if (isset($editors[$index])) return $editors[$index];
    if (!empty($editors)) return $editors[0];
    return [
        'id_utilizator' => $index + 1,
        'nume_complet' => 'Redactor demo ' . ($index + 1),
        'email' => 'redactor' . ($index + 1) . '@portal.md',
        'activ' => 1,
    ];
}
function procese_pick_category(array $ctx, int $index = 0): array {
    $categories = $ctx['categories'] ?? [];
    if (isset($categories[$index])) return $categories[$index];
    if (!empty($categories)) return $categories[0];
    return ['id_categorie' => 1, 'nume_categorie' => 'General', 'total_articole' => 0];
}
function procese_pick_node(array $ctx, int $index = 0): array {
    $nodes = $ctx['nodes'] ?? [];
    if (isset($nodes[$index])) return $nodes[$index];
    if (!empty($nodes)) return $nodes[0];
    return ['db_name' => 'gestiune_demo', 'table_count' => 0];
}
