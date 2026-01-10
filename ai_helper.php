<?php
// Fake AI backend for MediLink Stage 8
// No external API calls. Everything runs locally in PHP.
// Actions:
//   - summarize_case: produce bullet-style summary
//   - rewrite_post: rewrite text more professionally
//   - job_match: explain how well a job fits user's profile

session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

$action = $_GET['action'] ?? '';
$content = $data['content'] ?? '';
$jobText = $data['job_text'] ?? '';
$userRole = $data['user_role'] ?? '';
$userSpec = $data['specialization'] ?? '';
$userExp  = $data['experience_years'] ?? '';

function fake_bullets_from_text($text, $limit = 4) {
    // Very simple heuristic summarizer: split into sentences, take first few, clean up.
    $text = trim(strip_tags($text));
    $text = preg_replace('/\s+/', ' ', $text);
    $parts = preg_split('/(?<=[\.\!\?])\s+/', $text);
    $bullets = [];
    foreach ($parts as $p) {
        $p = trim($p);
        if ($p === '') continue;
        $bullets[] = $p;
        if (count($bullets) >= $limit) break;
    }
    if (empty($bullets)) {
        $bullets[] = 'Case details provided are limited; please review the original description.';
    }
    return $bullets;
}

function fake_rewrite($text) {
    $text = trim($text);
    if ($text === '') {
        return '';
    }
    // Simple "professionalization": ensure first letter caps, remove extra spaces, add polite tone.
    $text = preg_replace('/\s+/', ' ', $text);
    $text = ucfirst($text);
    if (!preg_match('/[\.!?]$/', $text)) {
        $text .= '.';
    }
    $text .= ' This description has been refined for clarity and professional communication.';
    return $text;
}

function fake_job_match($jobText, $userRole, $userSpec, $userExp) {
    $job = strtolower($jobText);
    $role = strtolower($userRole);
    $spec = strtolower($userSpec);
    $exp = (int)$userExp;

    $score = 0;
    $reasons = [];

    if ($role && strpos($job, $role) !== false) {
        $score += 3;
        $reasons[] = 'Job explicitly mentions your role.';
    }

    if ($spec && strpos($job, strtolower($spec)) !== false) {
        $score += 3;
        $reasons[] = 'Job description refers to your specialization.';
    }

    if ($exp >= 2 && (strpos($job, '2+') !== false || strpos($job, 'experience') !== false)) {
        $score += 2;
        $reasons[] = 'Your experience is suitable for the requested level.';
    }

    if (strpos($job, 'hospital') !== false || strpos($job, 'clinic') !== false) {
        $score += 1;
        $reasons[] = 'The setting (hospital/clinic) aligns with your background.';
    }

    if ($score >= 6) {
        $fit = 'High';
    } elseif ($score >= 3) {
        $fit = 'Medium';
    } else {
        $fit = 'Low';
    }

    if (empty($reasons)) {
        $reasons[] = 'Limited overlap detected; please review the job details manually.';
    }

    $suggestions = [];
    if ($fit === 'High') {
        $suggestions[] = 'Highlight relevant clinical cases and responsibilities in your cover letter.';
    } elseif ($fit === 'Medium') {
        $suggestions[] = 'Emphasize training, certifications, and adaptable skills when applying.';
    } else {
        $suggestions[] = 'Consider this role if you are exploring new areas, but also review more closely aligned positions.';
    }

    $out  = "AI Match: {$fit} fit for your profile.\n";
    $out .= "\nReasons:\n";
    foreach ($reasons as $r) {
        $out .= "- {$r}\n";
    }
    $out .= "\nSuggestions:\n";
    foreach ($suggestions as $s) {
        $out .= "- {$s}\n";
    }
    return $out;
}

if ($action === 'summarize_case') {
    if (!$content) {
        echo json_encode(['error' => 'No content provided']);
        exit;
    }
    $bullets = fake_bullets_from_text($content, 4);
    $summary = "AI Clinical Summary:\n";
    foreach ($bullets as $b) {
        $summary .= "- " . $b . "\n";
    }
    echo json_encode(['summary' => $summary]);
    exit;

} elseif ($action === 'rewrite_post') {
    if (!$content) {
        echo json_encode(['error' => 'No content provided']);
        exit;
    }
    $rewritten = fake_rewrite($content);
    echo json_encode(['summary' => $rewritten]);
    exit;

} elseif ($action === 'job_match') {
    if (!$jobText) {
        echo json_encode(['error' => 'No job description provided']);
        exit;
    }
    $summary = fake_job_match($jobText, $userRole, $userSpec, $userExp);
    echo json_encode(['summary' => $summary]);
    exit;

} else {
    echo json_encode(['error' => 'Unknown action']);
    exit;
}
?>
