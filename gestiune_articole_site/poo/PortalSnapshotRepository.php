<?php
declare(strict_types=1);
namespace PortalOOP;
class PortalSnapshotRepository
{
    public function fetch(array $currentUser = []): array
    {
        $counts = $this->firstRow('SELECT
            (SELECT COUNT(*) FROM ' . DB1 . '.utilizatori) AS total_users,
            (SELECT COUNT(*) FROM ' . DB2 . ".articole WHERE status='publicat') AS published_articles,
            (SELECT COUNT(*) FROM " . DB2 . ".articole WHERE status='draft') AS draft_articles,
            (SELECT COUNT(*) FROM " . DB3 . ".comentarii WHERE aprobat=1) AS approved_comments,
            (SELECT COUNT(*) FROM " . DB3 . '.vizualizari) AS total_views,
            (SELECT ROUND(AVG(nota),2) FROM ' . DB3 . '.evaluari) AS average_rating');
        $roles = q(DB1, 'SELECT r.denumire_rol AS role_name, COUNT(u.id_utilizator) AS total
            FROM roluri r
            LEFT JOIN utilizatori u USING(id_rol)
            GROUP BY r.id_rol
            ORDER BY total DESC, r.denumire_rol');
        $categories = q(DB2, 'SELECT c.nume_categorie AS category_name, COUNT(a.id_articol) AS total_articles
            FROM categorii c
            LEFT JOIN articole a USING(id_categorie)
            GROUP BY c.id_categorie
            ORDER BY total_articles DESC, c.nume_categorie');
        $topArticle = $this->firstRow('SELECT a.id_articol, a.titlu, a.numar_vizualizari, c.nume_categorie,
            CONCAT(u.prenume, " ", u.nume) AS author_name
            FROM ' . DB2 . '.articole a
            JOIN ' . DB2 . '.categorii c USING(id_categorie)
            LEFT JOIN ' . DB1 . '.utilizatori u ON u.id_utilizator = a.id_autor
            WHERE a.status = "publicat"
            ORDER BY a.numar_vizualizari DESC, a.id_articol ASC
            LIMIT 1');
        $latestArticle = $this->firstRow('SELECT a.id_articol, a.titlu, a.status, a.data_publicarii, c.nume_categorie,
            CONCAT(u.prenume, " ", u.nume) AS author_name
            FROM ' . DB2 . '.articole a
            JOIN ' . DB2 . '.categorii c USING(id_categorie)
            LEFT JOIN ' . DB1 . '.utilizatori u ON u.id_utilizator = a.id_autor
            ORDER BY COALESCE(a.data_publicarii, a.data_modificarii) DESC, a.id_articol DESC
            LIMIT 1');
        $topReader = $this->firstRow('SELECT CONCAT(u.prenume, " ", u.nume) AS reader_name,
            COUNT(DISTINCT c.id_comentariu) AS comments_count,
            COUNT(DISTINCT e.id_evaluare) AS ratings_count,
            (COUNT(DISTINCT c.id_comentariu) + COUNT(DISTINCT e.id_evaluare)) AS interaction_score
            FROM ' . DB1 . '.utilizatori u
            LEFT JOIN ' . DB3 . '.comentarii c ON c.id_utilizator = u.id_utilizator AND c.aprobat = 1
            LEFT JOIN ' . DB3 . '.evaluari e ON e.id_utilizator = u.id_utilizator
            WHERE u.id_rol = 3 AND u.activ = 1
            GROUP BY u.id_utilizator
            ORDER BY interaction_score DESC, reader_name ASC
            LIMIT 1');
        $schema = q(DB1, 'SELECT TABLE_SCHEMA AS db_name, COUNT(*) AS table_count
            FROM INFORMATION_SCHEMA.TABLES
            WHERE TABLE_SCHEMA IN ("' . DB1 . '", "' . DB2 . '", "' . DB3 . '")
              AND TABLE_TYPE = "BASE TABLE"
            GROUP BY TABLE_SCHEMA
            ORDER BY TABLE_SCHEMA');
        $engagementRate = 0.0;
        if ((int) ($counts['total_views'] ?? 0) > 0) {
            $engagementRate = round(((int) ($counts['approved_comments'] ?? 0) / (int) $counts['total_views']) * 100, 2);
        }
        return [
            'current_user' => $currentUser,
            'counts' => [
                'total_users' => (int) ($counts['total_users'] ?? 0),
                'published_articles' => (int) ($counts['published_articles'] ?? 0),
                'draft_articles' => (int) ($counts['draft_articles'] ?? 0),
                'approved_comments' => (int) ($counts['approved_comments'] ?? 0),
                'total_views' => (int) ($counts['total_views'] ?? 0),
                'average_rating' => (float) ($counts['average_rating'] ?? 0),
                'engagement_rate' => $engagementRate,
            ],
            'roles' => $roles,
            'categories' => $categories,
            'top_article' => $topArticle,
            'latest_article' => $latestArticle,
            'top_reader' => $topReader,
            'schema' => $schema,
        ];
    }
    private function firstRow(string $sql): array
    {
        $rows = q(DB1, $sql);
        return $rows[0] ?? [];
    }
}
