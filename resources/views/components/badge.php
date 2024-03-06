<?php

/** @var string $type */
/** @var string $content */
[$bgBadgeColor, $bgBadgeText] = match ($type) {
    'ERROR' => ['red', 'ERROR'],
    default => ['blue', 'INFO'],
};

?>

<div class="my-1">
    <span class="ml-2 px-1 bg-<?=$bgBadgeColor ?>-600 font-bold"><?=htmlspecialchars($bgBadgeText) ?></span>
    <span class="ml-1">
        <?=htmlspecialchars($content) ?>
    </span>
</div>
