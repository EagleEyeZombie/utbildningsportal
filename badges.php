<?php
require_once "include/header.php";

// --- SÄKERHETSVAKT ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// 1. Hämta XP
$stmt = $pdo->prepare("SELECT u_xp FROM users WHERE u_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();
$currentXP = $user['u_xp'] ?? 0;

// 2. Hämta ALLA achievements
$sql = "SELECT a.*, sa.sa_date_earned 
        FROM achievements a 
        LEFT JOIN student_achievements sa 
        ON a.a_id = sa.sa_achievement_fk AND sa.sa_student_fk = ?
        ORDER BY a.a_xp_required ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$userId]);
$allBadges = $stmt->fetchAll();

// 3. NYTT: Hämta framstegsdata för special-badges
$specialProgressData = $task_obj->getSpecialBadgeProgress($userId);

?>

<div class="container mt-4">
    
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h1 style="font-family: 'Cinzel Decorative', serif; color: var(--accent-gold); text-shadow: 2px 2px 4px #000;">
                Dina Utmärkelser
            </h1>
            <p class="text-white lead" style="text-shadow: 1px 1px 2px #000;">
                Samla XP och klara utmaningar för att låsa upp emblem!
            </p>
            <div class="badge bg-dark border border-warning p-2 fs-6">
                Din totala XP: <span style="color: var(--accent-gold);"><?php echo $currentXP; ?></span>
            </div>
        </div>
    </div>

    <div class="row">
        <?php foreach ($allBadges as $badge): 
            $isUnlocked = !empty($badge['sa_date_earned']);
            $xpReq = $badge['a_xp_required'];
            $badgeName = $badge['a_name'];
            
            // Är det en special-badge? (Krav över 90000)
            $isSpecial = ($xpReq >= 90000);

            // Förbered variabler för progress bar
            $currentVal = 0;
            $targetVal = 0;
            $percent = 0;
            $label = "XP";

            if ($isUnlocked) {
                $percent = 100;
            } else {
                if ($isSpecial) {
                    // Hämta data från vår nya funktion
                    if (isset($specialProgressData[$badgeName])) {
                        $data = $specialProgressData[$badgeName];
                        $currentVal = $data['current'];
                        $targetVal = $data['target'];
                        $label = $data['label'];
                        
                        if ($targetVal > 0) {
                            $percent = ($currentVal / $targetVal) * 100;
                        }
                    }
                } else {
                    // Vanlig XP-badge
                    $currentVal = floor($currentXP);
                    $targetVal = $xpReq;
                    $label = "XP";
                    
                    if ($xpReq > 0) {
                        $percent = ($currentXP / $xpReq) * 100;
                    }
                }
                // Säkra att procenten inte går över 100
                if ($percent > 100) $percent = 100;
            }
        ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm achievement-card <?php echo !$isUnlocked ? 'locked-badge' : ''; ?>">
                    <div class="card-body text-center p-4">
                        
                        <div class="achievement-icon mb-3">
                            <i class="bi <?php echo htmlspecialchars($badge['a_icon']); ?>"></i>
                        </div>

                        <h4 class="card-title fw-bold" style="font-family: 'Cinzel Decorative', serif; color: var(--accent-gold);">
                            <?php echo htmlspecialchars($badge['a_name']); ?>
                        </h4>

                        <p class="card-text text-light small mb-3">
                            <?php echo htmlspecialchars($badge['a_description']); ?>
                        </p>

                        <?php if ($isUnlocked): ?>
                            <div class="alert alert-success py-1 px-2 d-inline-block border-success" style="background: rgba(25, 135, 84, 0.3); font-size: 0.85rem;">
                                <i class="bi bi-check-circle-fill"></i> Upplåst <?php echo date('Y-m-d', strtotime($badge['sa_date_earned'])); ?>
                            </div>
                        <?php else: ?>
                            
                            <div class="progress-wrapper mt-3">
                                <div class="d-flex justify-content-between text-white-50 small mb-1">
                                    <span>Framsteg</span>
                                    <span><?php echo $currentVal; ?> / <?php echo $targetVal; ?> <?php echo $label; ?></span>
                                </div>
                                <div class="progress" style="height: 10px; background-color: rgba(255,255,255,0.1);">
                                    <div class="progress-bar bg-warning" role="progressbar" 
                                         style="width: <?php echo $percent; ?>%;" 
                                         aria-valuenow="<?php echo $percent; ?>" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                
                                <?php if ($percent >= 100): ?>
                                    <small class="text-warning d-block mt-2">Redo att låsas upp!</small>
                                <?php else: ?>
                                    <small class="text-muted d-block mt-2"><i class="bi bi-lock-fill"></i> Låst</small>
                                <?php endif; ?>
                            </div>

                        <?php endif; ?>

                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="row mt-3 mb-5">
        <div class="col-12 text-center">
            <a href="dashboard.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Tillbaka till Dashboard
            </a>
        </div>
    </div>

</div>

<?php require_once "include/footer.php"; ?>
