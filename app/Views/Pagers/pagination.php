<?php if ($pager->getPageCount() > 1): ?>
<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">

        <!-- Tombol Previous -->
        <?php if ($pager->hasPreviousPage()): ?>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getPreviousPage() ?>" aria-label="Previous">&laquo;</a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <span class="page-link">&laquo;</span>
            </li>
        <?php endif; ?>

        <!-- Nomor Halaman -->
        <?php foreach ($pager->links() as $link): ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a class="page-link" href="<?= $link['uri'] ?>">
                    <?= $link['title'] ?>
                </a>
            </li>
        <?php endforeach; ?>

        <!-- Tombol Next -->
        <?php if ($pager->hasNextPage()): ?>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getNextPage() ?>" aria-label="Next">&raquo;</a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <span class="page-link">&raquo;</span>
            </li>
        <?php endif; ?>

    </ul>
</nav>
<?php endif; ?>
