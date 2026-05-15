<?php
$file = 'resources/views/tecnico/dashboard.blade.php';
$content = file_get_contents($file);

// Replace the style block
$content = preg_replace(
    '/<style>.*?<\/style>/s', 
    '<link rel="stylesheet" href="{{ asset(\'css/tecnico.css\') }}?v={{ time() }}">'."\n".'    @vite([\'resources/css/app.css\', \'resources/js/app.js\'])', 
    $content
);

// Top Stats
$content = preg_replace('/<div style="display:flex; gap:20px; align-items:center; margin-bottom:25px; border-bottom:1px solid rgba\(255,255,255,0\.05\); padding-bottom:15px;">/', '<div class="top-bar-stats">', $content);
$content = preg_replace('/<div style="display:flex; flex-direction:column; gap:4px;">/', '<div class="stat-item">', $content);
$content = preg_replace('/<div style="font-size:0\.8rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0\.5px;">/', '<div class="stat-item-title">', $content);
$content = preg_replace('/<div style="font-size:1\.5rem;font-weight:700;color:#f8fafc;">/', '<div class="stat-item-value">', $content);

// Tabs
$content = preg_replace('/<div style="display:flex; gap:20px; border-bottom:1px solid rgba\(255,255,255,0\.05\); margin-bottom:20px; padding-top:10px;">/', '<div class="tabs-container">', $content);
$content = preg_replace('/style="background:none;border:none;padding:0 0 10px;font-family:\'Inter\',sans-serif;font-size:0\.95rem;font-weight:600;color:#f1f5f9;cursor:pointer;border-bottom:2px solid #3b82f6;margin-bottom:-1px;transition:all 0\.2s;"/', 'class="tab-btn active"', $content);
$content = preg_replace('/style="background:none;border:none;padding:0 0 10px;font-family:\'Inter\',sans-serif;font-size:0\.95rem;font-weight:600;color:#475569;cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-1px;transition:all 0\.2s;"/', 'class="tab-btn"', $content);
$content = preg_replace('/<span style="background:#334155;color:#94a3b8;font-size:0\.65rem;font-weight:700;padding:1px 6px;border-radius:999px;margin-left:5px;vertical-align:middle;">/', '<span class="tab-badge">', $content);

// Tickets
$content = str_replace('<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem;">', '<div class="ticket-top-row">', $content);
$content = str_replace('<div style="display:flex; align-items:center; gap:6px; color:#64748b; font-size:0.8rem; font-weight:600; letter-spacing:0.5px;">', '<div class="ticket-id">', $content);
$content = preg_replace('/style="border-radius:999px; font-size:0\.65rem; border:1px solid currentColor;"/', '', $content);
$content = str_replace('<div style="margin-bottom:1.25rem;">', '<div class="ticket-client-section">', $content);
$content = str_replace('<h2 style="font-size:1.35rem; font-weight:700; color:#ffffff; margin-bottom:0.2rem; letter-spacing:-0.02em;">', '<h2 class="ticket-client-name">', $content);
$content = str_replace('<p style="font-size:1rem; color:#94a3b8; margin:0;">', '<p class="ticket-problem-title">', $content);
$content = str_replace('<div style="display:flex; flex-direction:column; gap:0.85rem; margin-bottom:1.5rem;">', '<div class="ticket-details">', $content);
$content = str_replace('<div style="display:flex; align-items:flex-start; gap:12px;">', '<div class="detail-item">', $content);
$content = str_replace('<div style="display:flex; flex-direction:column; gap:2px;">', '<div class="detail-item-content">', $content);
$content = str_replace('<span style="font-size:0.95rem; color:#f1f5f9; font-weight:500;">', '<span class="detail-main-text">', $content);
$content = str_replace('<span style="font-size:0.85rem; color:#64748b;">', '<span class="detail-sub-text">', $content);
$content = str_replace('<div style="display:flex; align-items:center; gap:12px;">', '<div class="detail-item-center">', $content);
$content = str_replace('<div style="display:flex; align-items:flex-start; gap:12px; margin-top:0.25rem;">', '<div class="detail-item">', $content);
$content = str_replace('<span style="font-size:0.9rem; color:#94a3b8; line-height:1.5;">', '<span class="detail-desc-text">', $content);
$content = str_replace('<span style="font-size:0.6rem; color:#d97706; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">', '<span class="equipment-title">', $content);
$content = str_replace('<span style="font-size:0.9rem; color:#cbd5e1; line-height:1.4;">', '<span class="equipment-text">', $content);
$content = str_replace('<div style="border-top:1px solid rgba(255,255,255,0.05); margin-bottom:1.25rem;"></div>', '<div class="ticket-divider"></div>', $content);
$content = str_replace('<div style="display:flex; gap:10px;">', '<div class="ticket-actions">', $content);
$content = preg_replace('/style="flex:1; border-radius:999px; padding:0\.9rem; font-size:1rem; border:none; box-shadow:none;"/', '', $content);
$content = preg_replace('/class="btn-primary"/', 'class="btn-start"', $content); 
$content = str_replace('<div style="width:50px; height:50px; border-radius:50%; background:#1e293b; border:1px solid rgba(255,255,255,0.05); display:flex; align-items:center; justify-content:center; color:#94a3b8; cursor:pointer;"', '<div class="btn-circle"', $content);

// Pendientes
$content = str_replace('style="background:rgba(30, 41, 59, 0.4);backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,0.05);border-left:3px solid {{ $solidColor }};border-radius:14px;padding:1.25rem;display:flex;align-items:flex-start;gap:1rem;flex-wrap:wrap;transition:all 0.3s;{{ $glowBox }}"
                        onmouseover="this.style.background=\'rgba(30, 41, 59, 0.7)\';this.style.borderColor=\'rgba(255,255,255,0.1)\';" onmouseout="this.style.background=\'rgba(30, 41, 59, 0.4)\';this.style.borderColor=\'rgba(255,255,255,0.05)\';"', 'class="pendiente-card" style="border-left:3px solid {{ $solidColor }}; {{ $glowBox }}"', $content);
$content = str_replace('<div style="flex-shrink:0;margin-top:2px;">', '<div class="pendiente-icon">', $content);
$content = str_replace('<div style="flex:1;min-width:0;">', '<div class="pendiente-content">', $content);
$content = str_replace('<div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:6px;">', '<div class="pendiente-header">', $content);
$content = str_replace('<span style="font-weight:600;font-size:1.05rem;color:#f8fafc;letter-spacing:-0.01em;">', '<span class="pendiente-client">', $content);
$content = str_replace('style="background:rgba(51, 65, 85, 0.5);color:#cbd5e1;font-size:0.7rem;font-weight:700;padding:2px 8px;border-radius:6px;border:1px solid rgba(255,255,255,0.05);"', 'class="pendiente-tag"', $content);
$content = str_replace('style="background:{{ $badgeBg }};color:{{ $badgeColor }};font-size:0.7rem;font-weight:700;padding:2px 8px;border-radius:6px;border:1px solid rgba(255,255,255,0.05);"', 'class="pendiente-tag" style="background:{{ $badgeBg }};color:{{ $badgeColor }};"', $content);
$content = str_replace('<p style="font-size:0.85rem;color:#94a3b8;margin:0 0 8px;line-height:1.5;">', '<p class="pendiente-desc">', $content);
$content = str_replace('<div style="display:flex;flex-wrap:wrap;gap:12px;font-size:0.8rem;color:#64748b;">', '<div class="pendiente-footer">', $content);
$content = str_replace('<span style="display:flex;align-items:center;gap:4px;">', '<span class="pendiente-footer-item">', $content);

$content = str_replace('style="background:linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(5, 150, 105, 0.15) 100%);color:#34d399;border:1px solid rgba(16, 185, 129, 0.3);border-radius:8px;padding:8px 16px;font-size:0.85rem;font-weight:600;cursor:pointer;white-space:nowrap;transition:all 0.2s;box-shadow: 0 2px 10px rgba(16, 185, 129, 0.1);"
                                onmouseover="this.style.background=\'linear-gradient(135deg, #10b981 0%, #059669 100%)\';this.style.color=\'#fff\';this.style.boxShadow=\'0 4px 15px rgba(16, 185, 129, 0.4)\';" onmouseout="this.style.background=\'linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(5, 150, 105, 0.15) 100%)\';this.style.color=\'#34d399\';this.style.boxShadow=\'0 2px 10px rgba(16, 185, 129, 0.1)\';"', 'class="btn-complete"', $content);

file_put_contents($file, $content);
echo "Replaced inline styles with classes in dashboard.blade.php\n";
