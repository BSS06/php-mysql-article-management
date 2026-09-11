<?php
declare(strict_types=1);
namespace PortalOOP;
use DateTimeImmutable;
final class Formatter
{
    public static function pretty(mixed $value): string
    {
        if (is_scalar($value) || $value === null) {
            return (string) $value;
        }
        return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
    }
    public static function splitDecimal(float $value): array
    {
        $int = (int) floor(abs($value));
        $frac = (int) round((abs($value) - $int) * 100);
        return [$value < 0 ? -$int : $int, $frac];
    }
}
class PortalStaffSimple
{
    public string $name;
    public string $role;
    public int $articles;
}
class PortalStaffConstruct
{
    public function __construct(
        public string $name,
        public string $role,
        public int $articles
    ) {
    }
}
class EngagementFraction
{
    private int $whole;
    private int $fraction;
    public function __construct(int $whole = 0, int $fraction = 0)
    {
        $this->whole = $whole;
        $this->fraction = $fraction;
    }
    public static function fromFloat(float $value): self
    {
        [$whole, $fraction] = Formatter::splitDecimal($value);
        return new self($whole, $fraction);
    }
    public function toFloat(): float
    {
        $sign = ($this->whole < 0 || $this->fraction < 0) ? -1 : 1;
        $whole = abs($this->whole);
        $fraction = abs($this->fraction);
        return $sign * ($whole + $fraction / 100);
    }
    public function add(self $other): self
    {
        return self::fromFloat($this->toFloat() + $other->toFloat());
    }
    public function subtract(self $other): self
    {
        return self::fromFloat($this->toFloat() - $other->toFloat());
    }
    public function multiply(self $other): self
    {
        return self::fromFloat($this->toFloat() * $other->toFloat());
    }
    public function divide(self $other): ?self
    {
        if ($other->toFloat() == 0.0) {
            return null;
        }
        return self::fromFloat($this->toFloat() / $other->toFloat());
    }
    public function compare(self $other): int
    {
        return $this->toFloat() <=> $other->toFloat();
    }
    public function format(): string
    {
        return sprintf('%d.%02d', $this->whole, abs($this->fraction));
    }
}
class EngagementPoints
{
    private int $points;
    private int $subpoints;
    public function __construct(int $points = 0, int $subpoints = 0)
    {
        $total = $points * 100 + $subpoints;
        $this->points = intdiv($total, 100);
        $this->subpoints = abs($total % 100);
    }
    public static function fromRaw(int $raw): self
    {
        return new self(intdiv($raw, 100), abs($raw % 100));
    }
    public function totalSubpoints(): int
    {
        return $this->points * 100 + $this->subpoints;
    }
    public function add(self $other): self
    {
        return self::fromRaw($this->totalSubpoints() + $other->totalSubpoints());
    }
    public function subtract(self $other): self
    {
        return self::fromRaw($this->totalSubpoints() - $other->totalSubpoints());
    }
    public function divideBy(int $number): ?self
    {
        if ($number === 0) {
            return null;
        }
        return self::fromRaw((int) ($this->totalSubpoints() / $number));
    }
    public function multiplyBy(float $factor): self
    {
        return self::fromRaw((int) round($this->totalSubpoints() * $factor));
    }
    public function compare(self $other): int
    {
        return $this->totalSubpoints() <=> $other->totalSubpoints();
    }
    public function format(): string
    {
        return sprintf('%d puncte si %02d subpuncte', $this->points, $this->subpoints);
    }
}
class DashboardTrapezoid
{
    public function __construct(
        private float $x1,
        private float $y1,
        private float $x2,
        private float $y2,
        private float $x3,
        private float $y3,
        private float $x4,
        private float $y4
    ) {
    }
    private function distance(float $ax, float $ay, float $bx, float $by): float
    {
        return sqrt(($bx - $ax) ** 2 + ($by - $ay) ** 2);
    }
    public function perimeter(): float
    {
        return $this->distance($this->x1, $this->y1, $this->x2, $this->y2)
            + $this->distance($this->x2, $this->y2, $this->x3, $this->y3)
            + $this->distance($this->x3, $this->y3, $this->x4, $this->y4)
            + $this->distance($this->x4, $this->y4, $this->x1, $this->y1);
    }
    public function area(): float
    {
        $base1 = $this->distance($this->x1, $this->y1, $this->x2, $this->y2);
        $base2 = $this->distance($this->x3, $this->y3, $this->x4, $this->y4);
        $A = $this->y2 - $this->y1;
        $B = $this->x1 - $this->x2;
        $C = $this->x2 * $this->y1 - $this->x1 * $this->y2;
        $height = abs($A * $this->x3 + $B * $this->y3 + $C) / sqrt($A ** 2 + $B ** 2);
        return ($base1 + $base2) * $height / 2;
    }
    public function isIsosceles(): bool
    {
        $bc = round($this->distance($this->x2, $this->y2, $this->x3, $this->y3), 6);
        $da = round($this->distance($this->x4, $this->y4, $this->x1, $this->y1), 6);
        return $bc === $da;
    }
}
class InsightTriangle
{
    public function __construct(
        private float $x1,
        private float $y1,
        private float $x2,
        private float $y2,
        private float $x3,
        private float $y3
    ) {
    }
    private function distance(float $ax, float $ay, float $bx, float $by): float
    {
        return sqrt(($bx - $ax) ** 2 + ($by - $ay) ** 2);
    }
    public function perimeter(): float
    {
        return $this->distance($this->x1, $this->y1, $this->x2, $this->y2)
            + $this->distance($this->x2, $this->y2, $this->x3, $this->y3)
            + $this->distance($this->x3, $this->y3, $this->x1, $this->y1);
    }
    public function area(): float
    {
        $a = $this->distance($this->x1, $this->y1, $this->x2, $this->y2);
        $b = $this->distance($this->x2, $this->y2, $this->x3, $this->y3);
        $c = $this->distance($this->x3, $this->y3, $this->x1, $this->y1);
        $s = ($a + $b + $c) / 2;
        return sqrt(max(0.0, $s * ($s - $a) * ($s - $b) * ($s - $c)));
    }
    public function isIsosceles(): bool
    {
        $a = round($this->distance($this->x1, $this->y1, $this->x2, $this->y2), 6);
        $b = round($this->distance($this->x2, $this->y2, $this->x3, $this->y3), 6);
        $c = round($this->distance($this->x3, $this->y3, $this->x1, $this->y1), 6);
        return $a === $b || $b === $c || $a === $c;
    }
}
class CounterCalculator
{
    public function __construct(private int|float $left, private int|float $right)
    {
    }
    public function isInteger(int|float $value): bool
    {
        return is_numeric($value) && (int) $value == $value;
    }
    public function add(): ?int
    {
        if (!$this->isInteger($this->left) || !$this->isInteger($this->right)) {
            return null;
        }
        return (int) $this->left + (int) $this->right;
    }
    public function subtract(): ?int
    {
        if (!$this->isInteger($this->left) || !$this->isInteger($this->right)) {
            return null;
        }
        return (int) $this->left - (int) $this->right;
    }
}
class PublishingReminder
{
    private int $hour;
    private int $minute;
    public function __construct(int $hour = 7, int $minute = 0)
    {
        $this->setAlarm($hour, $minute);
    }
    public function isValid(int $hour, int $minute): bool
    {
        return $hour >= 0 && $hour <= 23 && $minute >= 0 && $minute <= 59;
    }
    public function setAlarm(int $hour, int $minute): void
    {
        if (!$this->isValid($hour, $minute)) {
            $this->hour = 0;
            $this->minute = 0;
            return;
        }
        $this->hour = $hour;
        $this->minute = $minute;
    }
    public function minutesRemaining(?DateTimeImmutable $now = null): int
    {
        $now ??= new DateTimeImmutable('now');
        $currentMinutes = ((int) $now->format('H')) * 60 + (int) $now->format('i');
        $targetMinutes = $this->hour * 60 + $this->minute;
        $difference = $targetMinutes - $currentMinutes;
        if ($difference < 0) {
            $difference += 1440;
        }
        return $difference;
    }
    public function format(): string
    {
        return sprintf('%02d:%02d', $this->hour, $this->minute);
    }
}
class TitleStringTool
{
    private string $left;
    private string $right;
    public function __construct(string $left = '', string $right = '')
    {
        $this->left = strtoupper(substr($left, 0, 5));
        $this->right = strtoupper(substr($right, 0, 5));
    }
    public function firstIsUppercase(): bool
    {
        $char = $this->left[0] ?? '';
        return $char !== '' && ctype_upper($char);
    }
    public function concatFive(): string
    {
        return substr($this->left . $this->right, 0, 5);
    }
    public function join(): string
    {
        return trim($this->left . ' ' . $this->right);
    }
    public function slug(): string
    {
        return trim((string) preg_replace('/-+/', '-', preg_replace('/[^a-z0-9]+/', '-', strtolower($this->join()))), '-');
    }
}
class PortalPerson
{
    public function __construct(
        public string $name,
        public string $role,
        public string $email,
        public bool $active
    ) {
    }
    public function reads(): string
    {
        return $this->name . ' citește articolele publicate.';
    }
    public function comments(): string
    {
        return $this->name . ' poate adăuga comentarii aprobabile.';
    }
    public function authenticates(): string
    {
        return $this->name . ' se autentifică pe portal.';
    }
}
class ReaderProfile
{
    public function __construct(
        public string $name,
        public string $segment,
        public int $year,
        public string $group,
        public float $activityScore = 0.0
    ) {
    }
    public function studies(): string
    {
        return $this->name . ' parcurge conținutul și își formează un traseu de învățare.';
    }
}
class AccessProfile
{
    private string $username;
    private string $passwordHash;
    private string $rank;
    private bool $authenticated = false;
    public function __construct(string $username, string $password, string $rank = 'user')
    {
        $this->username = $username;
        $this->passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $this->rank = $rank;
    }
    public function access(string $password): bool
    {
        $this->authenticated = password_verify($password, $this->passwordHash);
        return $this->authenticated;
    }
    public function changePassword(string $oldPassword, string $newPassword): bool
    {
        if (!$this->authenticated || !password_verify($oldPassword, $this->passwordHash)) {
            return false;
        }
        $this->passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        return true;
    }
    public function introduceUser(string $username, string $password, string $rank = 'user'): ?self
    {
        if ($this->rank !== 'admin') {
            return null;
        }
        return new self($username, $password, $rank);
    }
    public function status(): array
    {
        return [
            'username' => $this->username,
            'rank' => $this->rank,
            'authenticated' => $this->authenticated,
        ];
    }
}
class PortalNode
{
    public function __construct(
        public string $name,
        public int $tables,
        public string $purpose,
        public string $engine
    ) {
    }
}
class PortalSection extends PortalNode
{
    public function __construct(
        string $name,
        int $tables,
        string $purpose,
        string $engine,
        public int $items,
        public string $accessLevel,
        public string $code
    ) {
        parent::__construct($name, $tables, $purpose, $engine);
    }
}
class CrossNodeDatabase
{
    public function __construct(
        public string $name,
        public string $type,
        public string $host,
        public string $user,
        public string $password,
        public bool $connected = false
    ) {
    }
    public function connect(): void
    {
        $this->connected = true;
    }
    public function close(): void
    {
        $this->connected = false;
    }
}
class PortalTable extends CrossNodeDatabase
{
    public function __construct(
        string $name,
        string $type,
        string $host,
        string $user,
        string $password,
        public string $table,
        public int $columns,
        public string $engine
    ) {
        parent::__construct($name, $type, $host, $user, $password);
    }
}
class ArticleLibrary
{
    public function __construct(
        public string $name,
        public string $address,
        public int $articles,
        public int $founded,
        public string $type
    ) {
    }
    public function addArticles(int $value): void
    {
        $this->articles += $value;
    }
}
class ReadingRoom extends ArticleLibrary
{
    public function __construct(
        string $name,
        string $address,
        int $articles,
        int $founded,
        string $type,
        public int $places,
        public string $program,
        public string $owner
    ) {
        parent::__construct($name, $address, $articles, $founded, $type);
    }
}
class StorageUnit
{
    public function __construct(
        public int $capacity,
        public string $producer,
        public int $readSpeed,
        public int $writeSpeed,
        public string $interface
    ) {
    }
    public function writeSeconds(int $fileSizeMb): float
    {
        return round($fileSizeMb / max(1, $this->writeSpeed), 2);
    }
}
class EditorialFolder extends StorageUnit
{
    public function __construct(
        int $capacity,
        string $producer,
        int $readSpeed,
        int $writeSpeed,
        string $interface,
        public string $name,
        public int $files,
        public string $createdAt
    ) {
        parent::__construct($capacity, $producer, $readSpeed, $writeSpeed, $interface);
    }
    public function addFile(): void
    {
        $this->files++;
    }
}
class EditorialAdult
{
    public function __construct(
        public string $name,
        public int $age,
        public string $profession,
        public string $nationality,
        public string $status
    ) {
    }
}
class JuniorReader extends EditorialAdult
{
    public function __construct(
        string $name,
        int $age,
        string $profession,
        string $nationality,
        string $status,
        public string $school,
        public string $class,
        public string $hobby
    ) {
        parent::__construct($name, $age, $profession, $nationality, $status);
    }
}
class EncapsulatedStaff
{
    private string $name = '';
    private int $age = 0;
    private int $articles = 0;
    public function setName(string $name): void { $this->name = $name; }
    public function getName(): string { return $this->name; }
    public function setAge(int $age): void { $this->age = $age; }
    public function getAge(): int { return $this->age; }
    public function setArticles(int $articles): void { $this->articles = $articles; }
    public function getArticles(): int { return $this->articles; }
}
class ValidatedPortalUser
{
    private string $name = '';
    private int $accountAgeMonths = 0;
    private int $articles = 0;
    public function setName(string $name): void { $this->name = $name; }
    public function getName(): string { return $this->name; }
    public function setArticles(int $articles): void { $this->articles = $articles; }
    public function getArticles(): int { return $this->articles; }
    public function getAccountAgeMonths(): int { return $this->accountAgeMonths; }
    private function isValidAccountAge(int $months): bool
    {
        return $months >= 0 && $months <= 240;
    }
    public function setAccountAgeMonths(int $months): void
    {
        if ($this->isValidAccountAge($months)) {
            $this->accountAgeMonths = $months;
        }
    }
}
class BasePortalUser
{
    protected string $name = '';
    protected int $activity = 0;
    public function setName(string $name): void { $this->name = $name; }
    public function getName(): string { return $this->name; }
    public function setActivity(int $activity): void { $this->activity = $activity; }
    public function getActivity(): int { return $this->activity; }
}
class EditorUser extends BasePortalUser
{
    private int $published = 0;
    public function setPublished(int $published): void { $this->published = $published; }
    public function getPublished(): int { return $this->published; }
}
class ReaderUser extends BasePortalUser
{
    private int $ratings = 0;
    private int $level = 1;
    public function setRatings(int $ratings): void { $this->ratings = $ratings; }
    public function getRatings(): int { return $this->ratings; }
    public function setLevel(int $level): void { $this->level = $level; }
    public function getLevel(): int { return $this->level; }
}
class MobileEditor extends EditorUser
{
    private int $fieldExperience = 0;
    private string $deviceCategory = '';
    public function setFieldExperience(int $fieldExperience): void { $this->fieldExperience = $fieldExperience; }
    public function getFieldExperience(): int { return $this->fieldExperience; }
    public function setDeviceCategory(string $deviceCategory): void { $this->deviceCategory = $deviceCategory; }
    public function getDeviceCategory(): string { return $this->deviceCategory; }
}
class PortalSession
{
    private array $sessionData = [];
    public function set(string $key, mixed $value): void { $this->sessionData[$key] = $value; }
    public function get(string $key): mixed { return $this->sessionData[$key] ?? null; }
    public function delete(string $key): void { unset($this->sessionData[$key]); }
    public function has(string $key): bool { return array_key_exists($key, $this->sessionData); }
}
class FlashMessageManager
{
    public function __construct(private PortalSession $session)
    {
    }
    public function setMessage(string $message): void
    {
        $this->session->set('flash_message', $message);
    }
    public function getMessage(): ?string
    {
        $message = $this->session->get('flash_message');
        $this->session->delete('flash_message');
        return is_string($message) ? $message : null;
    }
}
class PortalZone
{
    public function __construct(protected string $name, protected int $size)
    {
    }
    public function getName(): string { return $this->name; }
    public function getSize(): int { return $this->size; }
}
class AudienceRegion extends PortalZone
{
    public function __construct(string $name, int $size, private int $population)
    {
        parent::__construct($name, $size);
    }
    public function getPopulation(): int { return $this->population; }
}
class EducationAudience extends PortalZone
{
    public function __construct(string $name, int $size, private int $graduates, private int $incomplete)
    {
        parent::__construct($name, $size);
    }
    public function getGraduates(): int { return $this->graduates; }
    public function getIncomplete(): int { return $this->incomplete; }
}
class ContentRegion
{
    public function __construct(
        public string $name,
        public int $surface,
        public int $population,
        public string $capital,
        public string $isoCode
    ) {
    }
    public function density(): float
    {
        return round($this->population / max(1, $this->surface), 2);
    }
}
class LocalCluster extends ContentRegion
{
    public function __construct(
        string $name,
        int $surface,
        int $population,
        string $capital,
        string $isoCode,
        public int $localities,
        public string $type,
        public string $lead
    ) {
        parent::__construct($name, $surface, $population, $capital, $isoCode);
    }
}
class PublishingMachine
{
    protected string $producer = '';
    protected string $processor = '';
    protected string $memory = '';
    public function setProducer(string $producer): void { $this->producer = $producer; }
    public function getProducer(): string { return $this->producer; }
    public function setProcessor(string $processor): void { $this->processor = $processor; }
    public function getProcessor(): string { return $this->processor; }
    public function setMemory(string $memory): void { $this->memory = $memory; }
    public function getMemory(): string { return $this->memory; }
}
class Workstation extends PublishingMachine
{
    private int $cost = 0;
    public function setCost(int $cost): void { $this->cost = $cost; }
    public function getCost(): int { return $this->cost; }
}
class SoftwareStack extends PublishingMachine
{
    private string $softwareName = '';
    private int $softwarePrice = 0;
    public function setSoftwareName(string $softwareName): void { $this->softwareName = $softwareName; }
    public function getSoftwareName(): string { return $this->softwareName; }
    public function setSoftwarePrice(int $softwarePrice): void { $this->softwarePrice = $softwarePrice; }
    public function getSoftwarePrice(): int { return $this->softwarePrice; }
}
class EditorialMentor
{
    private string $name = '';
    private int $experience = 0;
    private string $title = '';
    public function setName(string $name): void { $this->name = $name; }
    public function getName(): string { return $this->name; }
    public function setExperience(int $experience): void { $this->experience = $experience; }
    public function getExperience(): int { return $this->experience; }
    public function setTitle(string $title): void { $this->title = $title; }
    public function getTitle(): string { return $this->title; }
}
class EditorialTeam extends EditorialMentor
{
    private string $teamName = '';
    private int $members = 0;
    public function setTeamName(string $teamName): void { $this->teamName = $teamName; }
    public function getTeamName(): string { return $this->teamName; }
    public function setMembers(int $members): void { $this->members = $members; }
    public function getMembers(): int { return $this->members; }
}
