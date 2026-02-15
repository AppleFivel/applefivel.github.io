<?php
// 文件路径
$file = 'notes.json';

// 初始化：如果文件不存在，创建空数组
if (!file_exists($file)) {
    file_put_contents($file, json_encode([]));
}

// 处理新增
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['text'])) {
    $text = trim($_POST['text']);
    if ($text !== '') {
        // 读取现有记录
        $notes = json_decode(file_get_contents($file), true);
        
        // 新记录
        $newNote = [
            'text' => $text,
            'time' => date('Y-m-d H:i:s'),
            'id'   => time() . mt_rand(100,999)   // 简单唯一ID
        ];
        
        // 追加到数组最前面（最新在上）
        array_unshift($notes, $newNote);
        
        // 写回文件
        file_put_contents($file, json_encode($notes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        // PRG 防止重复提交
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// 读取所有记录
$notes = json_decode(file_exists($file) ? file_get_contents($file) : '[]', true);
?>

<!DOCTYPE html>
<html lang="zh">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>多条记事本（服务器版）</title>
  <style>
    body { font-family: system-ui, sans-serif; padding: 20px; max-width: 700px; margin: auto; line-height: 1.5; }
    textarea { width: 100%; height: 100px; padding: 12px; font-size: 16px; border: 1px solid #ccc; border-radius: 6px; resize: vertical; }
    button { padding: 10px 24px; font-size: 16px; background: #0066cc; color: white; border: none; border-radius: 6px; cursor: pointer; }
    button:hover { background: #0055aa; }
    .note { 
      background: #f8f9fa; 
      border: 1px solid #e0e0e0; 
      border-radius: 8px; 
      padding: 16px; 
      margin: 16px 0; 
      position: relative;
    }
    .note-time { color: #666; font-size: 0.9em; margin-bottom: 8px; }
    .note-text { white-space: pre-wrap; word-break: break-all; }
  </style>
</head>
<body>

  <h2>记事本（可多条保存，所有设备可见）</h2>
  
  <form method="POST">
    <textarea name="text" placeholder="输入想保存的内容..." required></textarea>
    <div style="margin-top:12px;">
      <button type="submit">保存这条记录</button>
    </div>
  </form>

  <h3 style="margin-top:40px;">已有记录（<?= count($notes) ?> 条）</h3>

  <?php if (empty($notes)): ?>
    <p style="color:#777;">还没有任何记录～</p>
  <?php else: ?>
    <?php foreach ($notes as $note): ?>
      <div class="note">
        <div class="note-time">📅 <?= htmlspecialchars($note['time']) ?></div>
        <div class="note-text"><?= nl2br(htmlspecialchars($note['text'])) ?></div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

</body>
</html>