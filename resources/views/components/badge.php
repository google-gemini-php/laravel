<?php

/** @var string $type */
/** @var string $content */
[$bgBadgeColor, $bgBadgeText] = match ($type) {
    'ERROR' => ['red', 'ERROR'],
    default => ['blue', 'INFO'],
};

?>

<div class="my-1">
    <span class="ml-2 px-1 bg-<?php echo $bgBadgeColor ?>-600 font-bold"><?php echo htmlspecialchars($bgBadgeText) ?></span>
    <span class="ml-1">
        <?php echo htmlspecialchars($content) ?>
    </span>
</div>