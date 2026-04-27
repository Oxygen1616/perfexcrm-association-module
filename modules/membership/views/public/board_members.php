<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
.member-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
    padding: 5px 0;
}
.member-grid .member-card,
.member-grid .board-card {
    flex: 0 0 calc(25% - 15px);   /* 4 per row */
    min-width: 0;
    display: flex;
    flex-direction: column;
    margin-bottom: 0;
}
@media (max-width: 991px) {
    .member-grid .member-card,
    .member-grid .board-card {
        flex: 0 0 calc(50% - 10px);  /* 2 per row */
    }
}
@media (max-width: 575px) {
    .member-grid .member-card,
    .member-grid .board-card {
        flex: 0 0 100%;               /* 1 per row */
    }
}
</style>
<?php
// Read card display settings (default: show all = 1)
function card_opt($key) {
    $v = get_option('membership_' . $key);
    return ($v === '' || $v === false) ? true : (bool)$v;
}
$show_photo      = card_opt('card_show_photo');
$show_type       = card_opt('card_show_membership_type');
$show_profession = card_opt('card_show_profession');
$show_email      = card_opt('card_show_email');
$show_phone      = card_opt('card_show_phone');
$show_status     = card_opt('card_show_status');

$bshow_photo     = card_opt('board_card_show_photo');
$bshow_position  = card_opt('board_card_show_position');
$bshow_election  = card_opt('board_card_show_election');
$bshow_email     = card_opt('board_card_show_email');
$bshow_phone     = card_opt('board_card_show_phone');
$bshow_status    = card_opt('board_card_show_status');

$members_per_page = (int)(get_option('membership_card_per_page') ?: 9);
$board_per_page   = (int)(get_option('membership_board_card_per_page') ?: 9);
?>

<div class="tw-flex tw-items-center tw-justify-between tw-flex-wrap tw-gap-3 tw-mb-1">
    <h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700 tw-mb-0"><?= _l('membership_members'); ?></h4>
    <div class="tw-flex tw-items-center tw-gap-2" style="min-width:240px;max-width:360px;flex:1;">
        <div class="input-group" style="width:100%;">
            <span class="input-group-addon"><i class="fa fa-search"></i></span>
            <input type="text" id="members-search" class="form-control"
                   placeholder="<?= _l('membership_search_members'); ?>">
            <span class="input-group-btn">
                <button class="btn btn-default" type="button" title="Clear">
                    <i class="fa fa-times"></i>
                </button>
            </span>
        </div>
    </div>
</div>
<p class="tw-text-sm tw-text-neutral-400 tw-mb-3" id="members-search-count"></p>

<!-- ── All Members ───────────────────────────────────────────────── -->
<div class="tw-mt-2 tw-mb-2">
    <?php if (!empty($members)): ?>
        <div id="members-no-results" class="panel_s" style="display:none;">
            <div class="panel-body text-center text-muted"><?= _l('membership_no_search_results'); ?></div>
        </div>
        <div id="members-grid" class="member-grid">
            <?php foreach ($members as $m): ?>
                <div class="member-card">
                    <div class="panel_s" style="flex:1;margin-bottom:0;">
                        <div class="panel-body text-center">
                            <?php if ($show_photo): ?>
                            <div class="tw-mb-3">
                                <img src="<?= e(contact_profile_image_url($m['contact_id'], 'thumb')); ?>"
                                     alt="<?= e($m['firstname'] . ' ' . $m['lastname']); ?>"
                                     class="img-circle"
                                     style="width:80px;height:80px;object-fit:cover;">
                            </div>
                            <?php endif; ?>
                            <h5 class="tw-font-semibold"><?= e($m['firstname'] . ' ' . $m['lastname']); ?></h5>
                            <?php if ($show_type): ?>
                            <p class="tw-font-medium tw-text-neutral-600"><?= e($m['membership_type'] ?: '-'); ?></p>
                            <?php endif; ?>
                            <?php if ($show_profession): ?>
                            <p class="tw-text-sm tw-text-neutral-400"><?= e($m['profession'] ?: '-'); ?></p>
                            <?php endif; ?>
                            <?php if ($show_email): ?>
                            <p class="tw-text-sm tw-text-neutral-500">
                                <i class="fa fa-envelope-o mright3"></i><?= e($m['email'] ?: '-'); ?>
                            </p>
                            <?php endif; ?>
                            <?php if ($show_phone): ?>
                            <p class="tw-text-sm tw-text-neutral-500">
                                <i class="fa fa-phone mright3"></i><?= e($m['phonenumber'] ?: '-'); ?>
                            </p>
                            <?php endif; ?>
                            <?php if ($show_status): ?>
                            <span class="label label-success"><?= _l('membership_active'); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="tw-flex tw-items-center tw-justify-between" style="margin-top:10px;margin-bottom:30px;" id="members-pagination">
            <span class="tw-text-sm tw-text-neutral-500" id="members-page-info"></span>
            <ul class="pagination pagination-sm tw-mb-0">
                <li id="members-prev"><a href="#">&laquo;</a></li>
                <li id="members-next"><a href="#">&raquo;</a></li>
            </ul>
        </div>
    <?php else: ?>
        <div class="panel_s tw-mb-8">
            <div class="panel-body">
                <p class="text-center text-muted"><?= _l('membership_no_members_found'); ?></p>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- ── Board Members ─────────────────────────────────────────────── -->
<div class="tw-flex tw-items-center tw-justify-between tw-flex-wrap tw-gap-3 tw-border-t tw-pt-6 tw-mb-1">
    <h4 class="tw-font-bold tw-text-lg tw-text-neutral-700 tw-mb-0"><?= _l('membership_board_members'); ?></h4>
    <div class="tw-flex tw-items-center tw-gap-2" style="min-width:240px;max-width:360px;flex:1;">
        <div class="input-group" style="width:100%;">
            <span class="input-group-addon"><i class="fa fa-search"></i></span>
            <input type="text" id="board-search" class="form-control"
                   placeholder="<?= _l('membership_search_board'); ?>">
            <span class="input-group-btn">
                <button class="btn btn-default" type="button" title="Clear">
                    <i class="fa fa-times"></i>
                </button>
            </span>
        </div>
    </div>
</div>
<p class="tw-text-sm tw-text-neutral-400 tw-mb-3" id="board-search-count"></p>

<div class="tw-mt-2">
    <?php if (!empty($board_members)): ?>
        <div id="board-no-results" class="panel_s" style="display:none;">
            <div class="panel-body text-center text-muted"><?= _l('membership_no_search_results'); ?></div>
        </div>
        <div id="board-grid" class="member-grid">
            <?php foreach ($board_members as $bm): ?>
                <div class="board-card">
                    <div class="panel_s" style="flex:1;margin-bottom:0;">
                        <div class="panel-body text-center">
                            <?php if ($bshow_photo): ?>
                            <div class="tw-mb-3">
                                <img src="<?= e(contact_profile_image_url($bm['contact_id'], 'thumb')); ?>"
                                     alt="<?= e($bm['firstname'] . ' ' . $bm['lastname']); ?>"
                                     class="img-circle"
                                     style="width:80px;height:80px;object-fit:cover;">
                            </div>
                            <?php endif; ?>
                            <h5 class="tw-font-semibold"><?= e($bm['firstname'] . ' ' . $bm['lastname']); ?></h5>
                            <?php if ($bshow_position): ?>
                            <p class="tw-font-medium tw-text-neutral-600"><?= e($bm['position'] ?: '-'); ?></p>
                            <?php endif; ?>
                            <?php if ($bshow_election): ?>
                            <p class="tw-text-sm tw-text-neutral-400"><?= e($bm['election_title'] ?: '-'); ?></p>
                            <?php endif; ?>
                            <?php if ($bshow_email): ?>
                            <p class="tw-text-sm tw-text-neutral-500">
                                <i class="fa fa-envelope-o mright3"></i><?= e($bm['email'] ?: '-'); ?>
                            </p>
                            <?php endif; ?>
                            <?php if ($bshow_phone): ?>
                            <p class="tw-text-sm tw-text-neutral-500">
                                <i class="fa fa-phone mright3"></i><?= e($bm['phonenumber'] ?: '-'); ?>
                            </p>
                            <?php endif; ?>
                            <?php if ($bshow_status): ?>
                            <span class="label label-<?= $bm['status'] == 'active' ? 'success' : 'default'; ?>">
                                <?= ucfirst($bm['status']); ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="tw-flex tw-items-center tw-justify-between" id="board-pagination">
            <span class="tw-text-sm tw-text-neutral-500" id="board-page-info"></span>
            <ul class="pagination pagination-sm tw-mb-0">
                <li id="board-prev"><a href="#">&laquo;</a></li>
                <li id="board-next"><a href="#">&raquo;</a></li>
            </ul>
        </div>
    <?php else: ?>
        <div class="panel_s">
            <div class="panel-body">
                <p class="text-center text-muted"><?= _l('membership_no_board_members'); ?></p>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// ── Self-contained grid factory (search + pagination fully isolated) ─
function makeGrid(gridId, searchId, countId, noResultsId, paginationId, prevId, nextId, infoId, perPage) {
    var grid = document.getElementById(gridId);
    if (!grid) return;

    // Private copies — each grid owns its own arrays
    var allCards = Array.from(grid.children);
    var visible  = allCards.slice();   // independent copy, not the same reference
    var page     = 1;

    function pages() { return Math.ceil(visible.length / perPage) || 1; }

    function render() {
        if (page > pages()) page = 1;
        var start = (page - 1) * perPage;
        var end   = start + perPage;

        allCards.forEach(function(c) { c.style.display = 'none'; });
        visible.forEach(function(c, i) {
            c.style.display = (i >= start && i < end) ? '' : 'none';
        });

        var noRes = document.getElementById(noResultsId);
        if (noRes) noRes.style.display = visible.length === 0 ? '' : 'none';

        var info = document.getElementById(infoId);
        if (info) info.textContent = visible.length > 0
            ? 'Page ' + page + ' of ' + pages() + ' (' + visible.length + ' found)' : '';

        var prevLi = document.getElementById(prevId);
        var nextLi = document.getElementById(nextId);
        if (prevLi) prevLi.className = page <= 1 ? 'disabled' : '';
        if (nextLi) nextLi.className = page >= pages() ? 'disabled' : '';

        var bar = document.getElementById(paginationId);
        if (bar) bar.style.display = visible.length <= perPage ? 'none' : '';
    }

    function applySearch(q) {
        q = (q || '').toLowerCase().trim();
        visible = q === ''
            ? allCards.slice()
            : allCards.filter(function(c) { return c.textContent.toLowerCase().indexOf(q) !== -1; });
        page = 1;

        var countEl = document.getElementById(countId);
        if (countEl) countEl.textContent = q !== ''
            ? (visible.length + ' result' + (visible.length !== 1 ? 's' : '') + ' for "' + q + '"') : '';

        render();

        // Re-equalise heights within this grid only
        var panels = grid.querySelectorAll('.panel_s');
        var max = 0;
        panels.forEach(function(p) { p.style.height = ''; max = Math.max(max, p.offsetHeight); });
        panels.forEach(function(p) { p.style.height = max + 'px'; });
    }

    // Search input
    var searchEl = document.getElementById(searchId);
    if (searchEl) {
        searchEl.addEventListener('input', function() { applySearch(this.value); });
        // Clear button (sibling inside input-group-btn)
        var clearBtn = searchEl.parentElement.querySelector('.input-group-btn button');
        if (clearBtn) clearBtn.addEventListener('click', function() {
            searchEl.value = '';
            applySearch('');
        });
    }

    // Pagination
    var prevLink = document.querySelector('#' + prevId + ' a');
    var nextLink = document.querySelector('#' + nextId + ' a');
    if (prevLink) prevLink.addEventListener('click', function(e) {
        e.preventDefault(); if (page > 1) { page--; render(); }
    });
    if (nextLink) nextLink.addEventListener('click', function(e) {
        e.preventDefault(); if (page < pages()) { page++; render(); }
    });

    render();
}

// ── Equalise heights on load ──────────────────────────────────────
function equaliseCardHeights(gridId) {
    var panels = document.querySelectorAll('#' + gridId + ' .panel_s');
    var max = 0;
    panels.forEach(function(p) { p.style.height = ''; max = Math.max(max, p.offsetHeight); });
    panels.forEach(function(p) { p.style.height = max + 'px'; });
}

window.addEventListener('load', function() {
    <?php if (!empty($members)): ?>
    makeGrid('members-grid','members-search','members-search-count','members-no-results',
             'members-pagination','members-prev','members-next','members-page-info', <?= (int)$members_per_page ?>);
    equaliseCardHeights('members-grid');
    <?php endif; ?>

    <?php if (!empty($board_members)): ?>
    makeGrid('board-grid','board-search','board-search-count','board-no-results',
             'board-pagination','board-prev','board-next','board-page-info', <?= (int)$board_per_page ?>);
    equaliseCardHeights('board-grid');
    <?php endif; ?>
});
</script>
