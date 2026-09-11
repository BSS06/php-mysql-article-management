<?php
declare(strict_types=1);
namespace PortalOOP;
use DateTimeImmutable;
class TaskRegistry
{
    public static function build(array $snapshot): array
    {
        $roles = $snapshot['roles'];
        $categories = $snapshot['categories'];
        $counts = $snapshot['counts'];
        $topArticle = $snapshot['top_article'];
        $latestArticle = $snapshot['latest_article'];
        $topReader = $snapshot['top_reader'];
        $firstRole = $roles[0] ?? ['role_name' => 'administrator', 'total' => 0];
        $secondRole = $roles[1] ?? ['role_name' => 'redactor', 'total' => 0];
        $firstCategory = $categories[0] ?? ['category_name' => 'General', 'total_articles' => 0];
        $secondCategory = $categories[1] ?? ['category_name' => 'Analiză', 'total_articles' => 0];
        $avgRating = EngagementFraction::fromFloat((float) $counts['average_rating']);
        $engagement = EngagementFraction::fromFloat((float) $counts['engagement_rate']);
        $fractionResult = [
            'medie_evaluari' => $avgRating->format(),
            'rata_engagement' => $engagement->format(),
            'suma' => $avgRating->add($engagement)->format(),
            'diferenta' => $avgRating->subtract($engagement)->format(),
            'comparatie' => $avgRating->compare($engagement),
        ];
        $pointsA = new EngagementPoints((int) $counts['approved_comments'], 50);
        $pointsB = new EngagementPoints((int) round((float) $counts['average_rating']), 25);
        $pointsResult = [
            'comentarii_aprobate' => $pointsA->format(),
            'calitate_editoriala' => $pointsB->format(),
            'total' => $pointsA->add($pointsB)->format(),
            'medie_pe_2' => $pointsA->divideBy(2)?->format(),
        ];
        $trap = new DashboardTrapezoid(0, 0, 8, 0, 6, 4, 2, 4);
        $triangle = new InsightTriangle(0, 0, 6, 0, 3, 4);
        $counter = new CounterCalculator((int) $counts['published_articles'], (int) $counts['draft_articles']);
        $reminder = new PublishingReminder(9, 0);
        $stringTool = new TitleStringTool((string) ($topArticle['titlu'] ?? 'Portal'), (string) ($firstCategory['category_name'] ?? 'News'));
        $person = new PortalPerson(
            (string) ($snapshot['current_user']['nume'] ?? 'Utilizator portal'),
            (string) ($snapshot['current_user']['rol'] ?? 'cititor'),
            (string) ($snapshot['current_user']['email'] ?? 'local@portal'),
            true
        );
        $reader = new ReaderProfile((string) ($topReader['reader_name'] ?? 'Cititor activ'), 'cititor', 2, 'COM', (float) ($topReader['interaction_score'] ?? 0));
        $access = new AccessProfile((string) ($snapshot['current_user']['email'] ?? 'admin@portal.md'), 'root', 'admin');
        $access->access('root');
        $access->changePassword('root', 'root2');
        $introduced = $access->introduceUser('redactie.noua@portal.md', 'secret', 'redactor');
        $node1 = new PortalNode('gestiune_utilizatori', (int) (($snapshot['schema'][0]['table_count'] ?? 0)), 'identitate si control acces', 'InnoDB');
        $section = new PortalSection('Tehnologie', (int) (($snapshot['schema'][1]['table_count'] ?? 0)), 'continut editorial', 'InnoDB', (int) $firstCategory['total_articles'], 'redactor', 'CAT-TECH');
        $db = new CrossNodeDatabase('portal_editorial', 'MySQL/MariaDB', '127.0.0.1', 'root', 'root');
        $db->connect();
        $table = new PortalTable('gestiune_continut', 'MySQL/MariaDB', '127.0.0.1', 'root', 'root', 'articole', 12, 'InnoDB');
        $table->connect();
        $library = new ArticleLibrary('Biblioteca articolelor', '/portal', (int) $counts['published_articles'], 2026, 'digitala');
        $library->addArticles((int) $counts['draft_articles']);
        $readingRoom = new ReadingRoom('Sala cititorilor', '/portal/lectura', (int) $counts['published_articles'], 2026, 'online', (int) $counts['total_users'], '24/7', 'echipa editoriala');
        $storage = new StorageUnit(128, 'Portal Storage', 180, 90, 'NVMe');
        $folder = new EditorialFolder(128, 'Portal Storage', 180, 90, 'NVMe', 'redactie', (int) $counts['draft_articles'], '2026-04-07');
        $folder->addFile();
        $adult = new EditorialAdult('Redacția centrală', 5, 'coordonare editorială', 'digitală', 'activă');
        $junior = new JuniorReader('Cititor nou', 1, 'consumator media', 'local', 'activ', 'portal.md', 'onboarding', 'comentarii');
        $encA = new EncapsulatedStaff();
        $encA->setName((string) ($topArticle['author_name'] ?? 'Autor principal'));
        $encA->setAge(3);
        $encA->setArticles((int) ($topArticle['numar_vizualizari'] ?? 0));
        $encB = new EncapsulatedStaff();
        $encB->setName((string) ($latestArticle['author_name'] ?? 'Autor secundar'));
        $encB->setAge(2);
        $encB->setArticles((int) $counts['draft_articles']);
        $validated = new ValidatedPortalUser();
        $validated->setName((string) ($snapshot['current_user']['nume'] ?? 'Utilizator'));
        $validated->setAccountAgeMonths(12);
        $validated->setArticles((int) $counts['published_articles']);
        $baseEditor = new EditorUser();
        $baseEditor->setName((string) ($topArticle['author_name'] ?? 'Editor'));
        $baseEditor->setActivity((int) ($topArticle['numar_vizualizari'] ?? 0));
        $baseEditor->setPublished((int) $counts['published_articles']);
        $baseReader = new ReaderUser();
        $baseReader->setName((string) ($topReader['reader_name'] ?? 'Cititor'));
        $baseReader->setActivity((int) ($topReader['interaction_score'] ?? 0));
        $baseReader->setRatings((int) ($topReader['ratings_count'] ?? 0));
        $baseReader->setLevel(2);
        $mobile = new MobileEditor();
        $mobile->setName('Reporter mobil');
        $mobile->setActivity((int) $counts['total_views']);
        $mobile->setPublished((int) $counts['published_articles']);
        $mobile->setFieldExperience(4);
        $mobile->setDeviceCategory('mobile');
        $session = new PortalSession();
        $session->set('user', $snapshot['current_user']['email'] ?? 'demo@portal');
        $session->set('role', $snapshot['current_user']['rol'] ?? 'administrator');
        $flash = new FlashMessageManager($session);
        $flash->setMessage('Articolul a fost publicat cu succes.');
        $flashOnce = $flash->getMessage();
        $flashTwice = $flash->getMessage();
        $regionA = new AudienceRegion('Tehnic', 5000, (int) $counts['total_views']);
        $regionB = new AudienceRegion('Generalist', 3000, (int) $counts['approved_comments']);
        $education = new EducationAudience('Public educațional', 33846, (int) $counts['published_articles'], (int) $counts['draft_articles']);
        $contentRegion = new ContentRegion('Nord editorial', 12000, (int) $counts['total_views'], 'Chișinău', 'MD-ED-N');
        $cluster = new LocalCluster('Cluster comentarii', 1831, (int) $counts['approved_comments'], 'Portal', 'MD-CL', 12, 'cluster', 'moderator');
        $workstationA = new Workstation();
        $workstationA->setProducer('Dell');
        $workstationA->setProcessor('Intel i7');
        $workstationA->setMemory('16GB');
        $workstationA->setCost(2200);
        $workstationB = new Workstation();
        $workstationB->setProducer('Lenovo');
        $workstationB->setProcessor('Intel i5');
        $workstationB->setMemory('8GB');
        $workstationB->setCost(1400);
        $software = new SoftwareStack();
        $software->setProducer('JetBrains');
        $software->setProcessor('N/A');
        $software->setMemory('N/A');
        $software->setSoftwareName('PhpStorm');
        $software->setSoftwarePrice(99);
        $mentor = new EditorialMentor();
        $mentor->setName('Mentor redacțional');
        $mentor->setExperience(8);
        $mentor->setTitle('editor coordonator');
        $team = new EditorialTeam();
        $team->setName('Coordonator echipă');
        $team->setExperience(10);
        $team->setTitle('lead editor');
        $team->setTeamName('Echipa conținut');
        $team->setMembers(max(1, (int) $counts['total_users']));
        return [
            [
                'folder' => 'Sarcina 1',
                'source' => '1.php',
                'title' => 'Clase simple pentru personalul portalului',
                'portal_model' => 'PortalStaffSimple și PortalStaffConstruct',
                'concept' => 'obiecte simple și constructor',
                'implementation' => Formatter::pretty([
                    'staff_public' => ['nume' => $firstRole['role_name'], 'rol' => 'rol dominant', 'articole' => (int) $firstRole['total']],
                    'staff_construct' => ['nume' => $secondRole['role_name'], 'rol' => 'rol secundar', 'articole' => (int) $secondRole['total']],
                ]),
            ],
            [
                'folder' => 'Sarcina 1',
                'source' => '01_Fractional.php',
                'title' => 'Fracții pentru KPI editoriali',
                'portal_model' => 'EngagementFraction',
                'concept' => 'operații pe valori fracționare',
                'implementation' => Formatter::pretty($fractionResult),
            ],
            [
                'folder' => 'Sarcina 1',
                'source' => '02_Bani.php',
                'title' => 'Puncte de engagement în loc de bani',
                'portal_model' => 'EngagementPoints',
                'concept' => 'normalizare unități și calcule',
                'implementation' => Formatter::pretty($pointsResult),
            ],
            [
                'folder' => 'Sarcina 1',
                'source' => '03_Trapez.php',
                'title' => 'Panou trapezoidal pentru design dashboard',
                'portal_model' => 'DashboardTrapezoid',
                'concept' => 'geometrie aplicată UI/analytics',
                'implementation' => Formatter::pretty([
                    'perimetru' => round($trap->perimeter(), 2),
                    'suprafata' => round($trap->area(), 2),
                    'isoscel' => $trap->isIsosceles(),
                ]),
            ],
            [
                'folder' => 'Sarcina 1',
                'source' => '04_Triunghi.php',
                'title' => 'Triunghi pentru blocul de insight vizual',
                'portal_model' => 'InsightTriangle',
                'concept' => 'măsurare geometrică în design',
                'implementation' => Formatter::pretty([
                    'perimetru' => round($triangle->perimeter(), 2),
                    'suprafata' => round($triangle->area(), 2),
                    'isoscel' => $triangle->isIsosceles(),
                ]),
            ],
            [
                'folder' => 'Sarcina 1',
                'source' => '05_Calculator.php',
                'title' => 'Calculator pentru contoare întregi',
                'portal_model' => 'CounterCalculator',
                'concept' => 'validare și operații pe întregi',
                'implementation' => Formatter::pretty([
                    'articole_publicate' => $counts['published_articles'],
                    'drafturi' => $counts['draft_articles'],
                    'suma' => $counter->add(),
                    'diferenta' => $counter->subtract(),
                ]),
            ],
            [
                'folder' => 'Sarcina 1',
                'source' => '06_Desteptator.php',
                'title' => 'Alarmă de publicare',
                'portal_model' => 'PublishingReminder',
                'concept' => 'validare oră și timp rămas',
                'implementation' => Formatter::pretty([
                    'ora_programata' => $reminder->format(),
                    'timp_ramas_minute' => $reminder->minutesRemaining(new DateTimeImmutable('now')),
                ]),
            ],
            [
                'folder' => 'Sarcina 1',
                'source' => '07_CalculatorSiruri.php',
                'title' => 'Prelucrare titlu și slug pentru portal',
                'portal_model' => 'TitleStringTool',
                'concept' => 'șiruri, uppercase, concatenare, slug',
                'implementation' => Formatter::pretty([
                    'join' => $stringTool->join(),
                    'concat_5' => $stringTool->concatFive(),
                    'slug' => $stringTool->slug(),
                    'prima_litera_majuscula' => $stringTool->firstIsUppercase(),
                ]),
            ],
            [
                'folder' => 'Sarcina 1',
                'source' => '08_Om.php',
                'title' => 'Persoană generică a portalului',
                'portal_model' => 'PortalPerson',
                'concept' => 'obiect cu stare și acțiuni',
                'implementation' => Formatter::pretty([
                    'profil' => ['nume' => $person->name, 'rol' => $person->role, 'email' => $person->email, 'activ' => $person->active],
                    'actiuni' => [$person->reads(), $person->comments(), $person->authenticates()],
                ]),
            ],
            [
                'folder' => 'Sarcina 1',
                'source' => '09_Student.php',
                'title' => 'Cititor ca profil de învățare',
                'portal_model' => 'ReaderProfile',
                'concept' => 'entitate cu comportament educațional',
                'implementation' => Formatter::pretty([
                    'profil' => ['nume' => $reader->name, 'segment' => $reader->segment, 'grupa' => $reader->group, 'scor' => $reader->activityScore],
                    'actiune' => $reader->studies(),
                ]),
            ],
            [
                'folder' => 'Sarcina 1',
                'source' => '10_Acces.php',
                'title' => 'Manager de acces pentru autentificare și roluri',
                'portal_model' => 'AccessProfile',
                'concept' => 'parolă, autentificare, creare utilizator',
                'implementation' => Formatter::pretty([
                    'status_curent' => $access->status(),
                    'utilizator_introdus' => $introduced?->status(),
                ]),
            ],
            [
                'folder' => 'Sarcina 2',
                'source' => 'sarcina_01.php',
                'title' => 'Variantă constructor pentru personal editorial',
                'portal_model' => 'PortalStaffConstruct',
                'concept' => 'constructor + obiecte multiple',
                'implementation' => Formatter::pretty([
                    'editor_1' => new PortalStaffConstruct((string) ($topArticle['author_name'] ?? 'Editor 1'), 'redactor', (int) $counts['published_articles']),
                    'editor_2' => new PortalStaffConstruct((string) ($latestArticle['author_name'] ?? 'Editor 2'), 'redactor', (int) $counts['draft_articles']),
                ]),
            ],
            [
                'folder' => 'Sarcina 2',
                'source' => 'sarcina_02.php',
                'title' => 'Noduri și secțiuni ale portalului',
                'portal_model' => 'PortalNode și PortalSection',
                'concept' => 'moștenire bază → specializare',
                'implementation' => Formatter::pretty([
                    'nod' => ['nume' => $node1->name, 'tabele' => $node1->tables, 'scop' => $node1->purpose],
                    'sectiune' => ['nume' => $section->name, 'itemi' => $section->items, 'cod' => $section->code, 'acces' => $section->accessLevel],
                ]),
            ],
            [
                'folder' => 'Sarcina 2',
                'source' => 'sarcina_03.php',
                'title' => 'Modelare reală a celor 3 baze cross-node',
                'portal_model' => 'CrossNodeDatabase și PortalTable',
                'concept' => 'BD + tabele ca obiecte',
                'implementation' => Formatter::pretty([
                    'conexiune_bd' => ['nume' => $db->name, 'tip' => $db->type, 'conectat' => $db->connected],
                    'tabel' => ['baza' => $table->name, 'tabel' => $table->table, 'coloane' => $table->columns, 'motor' => $table->engine],
                ]),
            ],
            [
                'folder' => 'Sarcina 2',
                'source' => 'sarcina_04.php',
                'title' => 'Biblioteca de articole și sala de lectură',
                'portal_model' => 'ArticleLibrary și ReadingRoom',
                'concept' => 'colecție + zonă specializată',
                'implementation' => Formatter::pretty([
                    'biblioteca' => ['nume' => $library->name, 'articole_dupa_actualizare' => $library->articles, 'tip' => $library->type],
                    'sala' => ['nume' => $readingRoom->name, 'locuri' => $readingRoom->places, 'program' => $readingRoom->program],
                ]),
            ],
            [
                'folder' => 'Sarcina 2',
                'source' => 'sarcina_05.php',
                'title' => 'Stocare și mapă de lucru editorială',
                'portal_model' => 'StorageUnit și EditorialFolder',
                'concept' => 'compoziție hardware/fișiere',
                'implementation' => Formatter::pretty([
                    'scriere_500mb_sec' => $storage->writeSeconds(500),
                    'mapa' => ['nume' => $folder->name, 'fisiere' => $folder->files, 'creata' => $folder->createdAt],
                ]),
            ],
            [
                'folder' => 'Sarcina 2',
                'source' => 'sarcina_06.php',
                'title' => 'Model de utilizator senior și utilizator nou',
                'portal_model' => 'EditorialAdult și JuniorReader',
                'concept' => 'moștenire pe profiluri',
                'implementation' => Formatter::pretty([
                    'senior' => ['nume' => $adult->name, 'profesie' => $adult->profession, 'status' => $adult->status],
                    'junior' => ['nume' => $junior->name, 'scoala' => $junior->school, 'hobby' => $junior->hobby],
                ]),
            ],
            [
                'folder' => 'Sarcina 2',
                'source' => 'sarcina_07.php',
                'title' => 'Încapsulare pentru autori și volum de conținut',
                'portal_model' => 'EncapsulatedStaff',
                'concept' => 'getteri/setteri privați',
                'implementation' => Formatter::pretty([
                    'autor_1' => ['nume' => $encA->getName(), 'vechime' => $encA->getAge(), 'indicator' => $encA->getArticles()],
                    'autor_2' => ['nume' => $encB->getName(), 'vechime' => $encB->getAge(), 'indicator' => $encB->getArticles()],
                ]),
            ],
            [
                'folder' => 'Sarcina 2',
                'source' => 'sarcina_08.php',
                'title' => 'Validare pentru vechimea contului',
                'portal_model' => 'ValidatedPortalUser',
                'concept' => 'validare internă privată',
                'implementation' => Formatter::pretty([
                    'utilizator' => $validated->getName(),
                    'vechime_cont_luni' => $validated->getAccountAgeMonths(),
                    'articole' => $validated->getArticles(),
                ]),
            ],
            [
                'folder' => 'Sarcina 2',
                'source' => 'sarcina_09.php',
                'title' => 'Utilizator de bază, editor și cititor',
                'portal_model' => 'BasePortalUser, EditorUser, ReaderUser',
                'concept' => 'ierarhie simplă de roluri',
                'implementation' => Formatter::pretty([
                    'editor' => ['nume' => $baseEditor->getName(), 'publicate' => $baseEditor->getPublished(), 'activitate' => $baseEditor->getActivity()],
                    'cititor' => ['nume' => $baseReader->getName(), 'evaluari' => $baseReader->getRatings(), 'nivel' => $baseReader->getLevel()],
                ]),
            ],
            [
                'folder' => 'Sarcina 2',
                'source' => 'sarcina_10.php',
                'title' => 'Editor mobil / reporter de teren',
                'portal_model' => 'MobileEditor',
                'concept' => 'extindere pe capabilități speciale',
                'implementation' => Formatter::pretty([
                    'nume' => $mobile->getName(),
                    'publicate' => $mobile->getPublished(),
                    'experienta_teren' => $mobile->getFieldExperience(),
                    'categorie_dispozitiv' => $mobile->getDeviceCategory(),
                ]),
            ],
            [
                'folder' => 'Sarcina 2',
                'source' => 'sarcina_11.php',
                'title' => 'Sesiune și flash message integrate în portal',
                'portal_model' => 'PortalSession și FlashMessageManager',
                'concept' => 'stare temporară și mesaj unic',
                'implementation' => Formatter::pretty([
                    'user' => $session->get('user'),
                    'role' => $session->get('role'),
                    'flash_prima_citire' => $flashOnce,
                    'flash_a_doua_citire' => $flashTwice,
                ]),
            ],
            [
                'folder' => 'Sarcina 2',
                'source' => 'sarcina_12.php',
                'title' => 'Zone de audiență și profil educațional',
                'portal_model' => 'PortalZone, AudienceRegion, EducationAudience',
                'concept' => 'moștenire cu atribute specializate',
                'implementation' => Formatter::pretty([
                    'regiune_1' => ['nume' => $regionA->getName(), 'populatie' => $regionA->getPopulation()],
                    'regiune_2' => ['nume' => $regionB->getName(), 'populatie' => $regionB->getPopulation()],
                    'educational' => ['nume' => $education->getName(), 'absolventi' => $education->getGraduates(), 'neterminate' => $education->getIncomplete()],
                ]),
            ],
            [
                'folder' => 'Sarcina 2',
                'source' => 'sarcina_13.php',
                'title' => 'Regiuni de conținut și clustere locale',
                'portal_model' => 'ContentRegion și LocalCluster',
                'concept' => 'specializare administrativă',
                'implementation' => Formatter::pretty([
                    'regiune' => ['nume' => $contentRegion->name, 'densitate' => $contentRegion->density(), 'cod' => $contentRegion->isoCode],
                    'cluster' => ['nume' => $cluster->name, 'localitati' => $cluster->localities, 'lead' => $cluster->lead],
                ]),
            ],
            [
                'folder' => 'Sarcina 2',
                'source' => 'sarcina_14.php',
                'title' => 'Stații de lucru și software editorial',
                'portal_model' => 'Workstation și SoftwareStack',
                'concept' => 'hardware + software ca obiecte',
                'implementation' => Formatter::pretty([
                    'pc_1' => ['producator' => $workstationA->getProducer(), 'procesor' => $workstationA->getProcessor(), 'cost' => $workstationA->getCost()],
                    'pc_2' => ['producator' => $workstationB->getProducer(), 'procesor' => $workstationB->getProcessor(), 'cost' => $workstationB->getCost()],
                    'soft' => ['nume' => $software->getSoftwareName(), 'pret' => $software->getSoftwarePrice(), 'producator' => $software->getProducer()],
                ]),
            ],
            [
                'folder' => 'Sarcina 2',
                'source' => 'sarcina_15.php',
                'title' => 'Mentor și echipă editorială',
                'portal_model' => 'EditorialMentor și EditorialTeam',
                'concept' => 'coordonare și grupare',
                'implementation' => Formatter::pretty([
                    'mentor' => ['nume' => $mentor->getName(), 'titlu' => $mentor->getTitle(), 'experienta' => $mentor->getExperience()],
                    'echipa' => ['nume' => $team->getName(), 'echipa' => $team->getTeamName(), 'membri' => $team->getMembers()],
                ]),
            ],
        ];
    }
}
