<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $tree array */
/* @var $continentRules app\modules\backend\models\AsnBlockRule[] */
/* @var $countryRules   app\modules\backend\models\AsnBlockRule[] */
/* @var $globalEnabled  int */
/* @var $errorCode      string */
/* @var $randomError    int */
/* @var $totalAsns      int */
/* @var $totalEnabled   int */
/* @var $totalBlocked   int */
/* @var $allowedCodes   string[] */
/* @var $adminAsn       int|null */
/* @var $botsEnabled    int */
/* @var $botCabinet     int */
/* @var $botStats       int */
/* @var $botPage        int */
/* @var $botRegister    int */
/* @var $botLogin       int */

$this->title = Yii::t('backend', 'ASN Белый список');
$isEn = substr(Yii::$app->language, 0, 2) === 'en';

$urls = [
    'setGlobal'       => Url::to(['/backend/asn-whitelist/set-global']),
    'setErrorCode'    => Url::to(['/backend/asn-whitelist/set-error-code']),
    'setSearchBots'   => Url::to(['/backend/asn-whitelist/set-search-bots']),
    'toggleContinent' => Url::to(['/backend/asn-whitelist/toggle-continent']),
    'toggleCountry'   => Url::to(['/backend/asn-whitelist/toggle-country']),
    'toggleAsn'       => Url::to(['/backend/asn-whitelist/toggle-asn']),
    'loadCountryAsns' => Url::to(['/backend/asn-whitelist/load-country-asns']),
    'search'          => Url::to(['/backend/asn-whitelist/search']),
    'showBlocked'     => Url::to(['/backend/asn-whitelist/show-blocked']),
];

$langStrings = [
    'loading'         => Yii::t('backend', 'Загрузка...'),
    'error_load'      => Yii::t('backend', 'Ошибка загрузки'),
    'no_records'      => Yii::t('backend', 'Нет записей'),
    'searching'       => Yii::t('backend', 'Поиск...'),
    'error'           => Yii::t('backend', 'Ошибка'),
    'not_found'       => Yii::t('backend', 'Ничего не найдено'),
    'show_countries'  => Yii::t('backend', 'Показать страны'),
    'hide_countries'  => Yii::t('backend', 'Скрыть страны'),
    'show_asn'        => Yii::t('backend', 'Показать ASN'),
    'hide_asn'        => Yii::t('backend', 'Скрыть ASN'),
    'allow_whitelist' => Yii::t('backend', 'Активен (доступ только по белому списку)'),
    'allow_all'       => Yii::t('backend', 'Отключён (доступ разрешён всем)'),
    'filter_on'       => Yii::t('backend', 'Фильтр: АКТИВЕН'),
    'filter_off'      => Yii::t('backend', 'Фильтр: ОТКЛЮЧЁН'),
    'search_results'  => Yii::t('backend', 'Результаты поиска'),
    'blocked_title'   => Yii::t('backend', 'Заблокированные ASN'),
    'tooltip_allow'   => Yii::t('backend', 'Разрешён (в белом списке). Нажмите, чтобы заблокировать'),
    'tooltip_block'   => Yii::t('backend', 'Заблокирован. Нажмите, чтобы разрешить'),
    'protected_asn'   => Yii::t('backend', 'Ваш ASN защищён'),
    'settings'        => Yii::t('backend', 'Настройки'),
    'close'           => Yii::t('backend', 'Закрыть'),
    'save_error'      => Yii::t('backend', 'Ошибка сохранения'),
];

$jsonUrls  = \yii\helpers\Json::encode($urls);
$jsonLang  = \yii\helpers\Json::encode($langStrings);
$csrfParam = Yii::$app->request->csrfParam;
$csrfToken = Yii::$app->request->csrfToken;

$this->registerJs(
    "window.__ASN_URLS = {$jsonUrls};\n"
    . "window.__ASN_LANG = {$jsonLang};\n"
    . "window.__ADMIN_ASN = " . (int)$adminAsn . ";\n"
    . "window.__ASN_CSRF = " . \yii\helpers\Json::encode(['name' => $csrfParam, 'token' => $csrfToken]) . ";",
    \yii\web\View::POS_HEAD,
    'asn-urls'
);
?>

<div class="asn-filter-page">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0"><?= Html::encode($this->title) ?></h1>
        <span class="badge bg-<?= $globalEnabled ? 'success' : 'danger' ?> fs-6" id="global-badge">
            <?= $globalEnabled ? Yii::t('backend', 'Фильтр: АКТИВЕН') : Yii::t('backend', 'Фильтр: ОТКЛЮЧЁН') ?>
        </span>
    </div>

    <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
        <?= Yii::t('backend', 'Внимание этот модуль по умолчанию запрещает доступ всем серверам к сайту, кроме ваших игровых и логин серверов. Все остальные интернет провайдеры имеют доступ по умолчанию, при необходимости можно отключить полностью или на выбор.') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <?php if (!empty($adminAsn)): ?>
        <div class="alert alert-info py-2 mb-4">
            <i class="fas fa-shield-alt"></i>
            <?= Yii::t('backend', 'Ваш ASN') ?>: <b>AS<?= $adminAsn ?></b>. 
            <?= Yii::t('backend', 'Он защищён от случайной блокировки.') ?>
        </div>
    <?php else: ?>
        <div class="alert alert-secondary py-2 mb-4">
            <?= Yii::t('backend', 'Не удалось автоматически определить ваш ASN. Если вы заблокируете своего провайдера, вы потеряете доступ.') ?>
        </div>
    <?php endif; ?>

    <!-- Основные настройки -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card h-100"><div class="card-body">
                <h6 class="card-title"><?= Yii::t('backend', 'Глобальный рубильник') ?></h6>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="global-toggle" <?= $globalEnabled ? 'checked' : '' ?>>
                    <label class="form-check-label" for="global-toggle" id="global-toggle-label">
                        <?= $globalEnabled ? Yii::t('backend', 'Активен (доступ только по белому списку)') : Yii::t('backend', 'Отключён (доступ разрешён всем)') ?>
                    </label>
                </div>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card h-100"><div class="card-body">
                <h6 class="card-title"><?= Yii::t('backend', 'Код ошибки') ?></h6>
                <select class="form-select" id="error-code">
                    <?php foreach ($allowedCodes as $code): ?>
                        <option value="<?= Html::encode($code) ?>" <?= $errorCode === $code ? 'selected' : '' ?>><?= Html::encode($code) ?></option>
                    <?php endforeach; ?>
                </select>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card h-100"><div class="card-body">
                <h6 class="card-title"><?= Yii::t('backend', 'Случайный код') ?></h6>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="random-error" <?= $randomError ? 'checked' : '' ?>>
                    <label class="form-check-label" for="random-error"><?= Yii::t('backend', 'Случайный 4xx/5xx') ?></label>
                </div>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card h-100"><div class="card-body">
                <h6 class="card-title"><?= Yii::t('backend', 'Сводка') ?></h6>
                <div><small><?= Yii::t('backend', 'Всего ASN:') ?> <b><?= (int)$totalAsns ?></b></small></div>
                <div><small><?= Yii::t('backend', 'Включено в белый список:') ?> <b class="text-success"><?= (int)$totalEnabled ?></b></small></div>
                <div><small><?= Yii::t('backend', 'Заблокировано:') ?> <b class="text-danger" id="summary-blocked"><?= (int)$totalBlocked ?></b></small></div>
            </div></div>
        </div>
    </div>

    <!-- Блок SEO и Поисковых ботов -->
    <div class="card mb-4 border-info">
        <div class="card-header bg-info bg-opacity-10 d-flex align-items-center flex-wrap gap-2">
            <h5 class="mb-0 text-info me-auto"><i class="fas fa-robot"></i> <?= Yii::t('backend', 'SEO и Поисковые боты') ?></h5>
            
            <button type="button" class="btn btn-sm btn-outline-info" id="btn-toggle-bot-settings">
                <?= Yii::t('backend', 'Настройки') ?>
            </button>

            <div class="form-check form-switch mb-0 ms-2">
                <input class="form-check-input" type="checkbox" id="bots-enabled" <?= $botsEnabled ? 'checked' : '' ?>>
                <label class="form-check-label fw-bold" for="bots-enabled"><?= Yii::t('backend', 'Разрешить доступ') ?></label>
            </div>
        </div>
        
        <!-- Блок с настройками скрыт по умолчанию -->
        <div class="card-body" id="bots-settings" style="display:none;">
            <p class="text-muted small mb-3">
                <?= Yii::t('backend', 'Главная страница (/) всегда открыта для поисковых ботов при включении этой опции. Ниже вы можете точечно разрешить доступ к другим разделам.') ?>
            </p>
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input bot-toggle" type="checkbox" id="bot-cabinet" data-param="asn_filter.search_bots_cabinet" <?= $botCabinet ? 'checked' : '' ?>>
                        <label class="form-check-label" for="bot-cabinet">/cabinet</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input bot-toggle" type="checkbox" id="bot-stats" data-param="asn_filter.search_bots_stats" <?= $botStats ? 'checked' : '' ?>>
                        <label class="form-check-label" for="bot-stats">/stats</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input bot-toggle" type="checkbox" id="bot-page" data-param="asn_filter.search_bots_page" <?= $botPage ? 'checked' : '' ?>>
                        <label class="form-check-label" for="bot-page">/page/*</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input bot-toggle" type="checkbox" id="bot-register" data-param="asn_filter.search_bots_register" <?= $botRegister ? 'checked' : '' ?>>
                        <label class="form-check-label text-danger" for="bot-register">/register</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input bot-toggle" type="checkbox" id="bot-login" data-param="asn_filter.search_bots_login" <?= $botLogin ? 'checked' : '' ?>>
                        <label class="form-check-label text-danger" for="bot-login">/login</label>
                    </div>
                </div>
            </div>
            <div class="alert alert-danger mt-3 mb-0 py-2 small">
                <i class="fas fa-exclamation-triangle"></i>
                <?= Yii::t('backend', 'Внимание! Открытие форм регистрации (/register) и авторизации (/login) для поисковых ботов несет повышенный риск (спам-регистрации, брутфорс). Делайте это только в случае крайней необходимости.') ?>
            </div>
        </div>
    </div>

    <!-- Фильтры -->
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <select id="filter-continent" class="form-select">
                <option value=""><?= Yii::t('backend', 'Все континенты') ?></option>
                <?php foreach ($tree as $contKey => $cont): ?>
                    <?php $contName = $isEn ? $cont['name_en'] : $cont['name_ru']; ?>
                    <option value="<?= Html::encode($contKey) ?>"><?= Html::encode($contName) ?> (<?= count($cont['countries']) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select id="filter-country" class="form-select" disabled>
                <option value=""><?= Yii::t('backend', 'Все страны') ?></option>
                <?php foreach ($tree as $contKey => $cont): ?>
                    <?php foreach ($cont['countries'] as $code => $c): ?>
                        <?php $ctryName = $isEn ? $c['name_en'] : $c['name_ru']; ?>
                        <option value="<?= Html::encode($code) ?>" data-continent="<?= Html::encode($contKey) ?>" hidden><?= Html::encode($ctryName) ?></option>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6 d-flex gap-2">
            <input type="text" id="filter-search" class="form-control" placeholder="<?= Yii::t('backend', 'Поиск...') ?>">
            <button type="button" id="btn-show-blocked" class="btn btn-outline-danger text-nowrap">
                <?= Yii::t('backend', 'Показать заблокированные') ?>
            </button>
        </div>
    </div>

    <!-- Дерево -->
    <div id="asn-tree">
        <?php foreach ($tree as $contKey => $cont): ?>
            <?php
                $contName     = $isEn ? $cont['name_en'] : $cont['name_ru'];
                $contRule     = $continentRules[$contKey] ?? null;
                $contEnabled  = $contRule === null ? 1 : (int)$contRule->enabled;
                $totalContAsn = 0;
                foreach ($cont['countries'] as $c) { $totalContAsn += $c['asn_count']; }
                $tooltip = $contEnabled ? Yii::t('backend', 'tooltip_allow') : Yii::t('backend', 'tooltip_block');
            ?>
            <div class="card mb-2 continent-block" data-continent="<?= Html::encode($contKey) ?>">
                <div class="card-header d-flex align-items-center flex-wrap gap-2">
                    <div class="form-check form-switch mb-0" title="<?= $tooltip ?>">
                        <input class="form-check-input" type="checkbox" data-toggle-type="continent" data-value="<?= Html::encode($contKey) ?>" <?= $contEnabled ? 'checked' : '' ?>>
                    </div>
                    <b><?= Html::encode($contName) ?></b>
                    <span class="badge bg-secondary"><?= count($cont['countries']) ?> <?= Yii::t('backend', 'стран') ?></span>
                    <span class="badge bg-secondary"><?= (int)$totalContAsn ?> ASN</span>
                    <?php if (!empty($cont['blocked_count'])): ?>
                        <span class="badge bg-danger" title="<?= Yii::t('backend', 'Заблокировано ASN') ?>">
                            <?= Yii::t('backend', 'Заблокировано:') ?> <?= $cont['blocked_count'] ?>
                        </span>
                    <?php endif; ?>
                    <button type="button" class="btn btn-sm btn-outline-primary ms-auto btn-expand-continent"><?= Yii::t('backend', 'Показать страны') ?></button>
                </div>
                <div class="card-body continent-countries" style="display:none;">
                    <?php foreach ($cont['countries'] as $code => $c): ?>
                        <?php
                            $ctryName    = $isEn ? $c['name_en'] : $c['name_ru'];
                            $ctryRule    = $countryRules[$code] ?? null;
                            $ctryEnabled = $ctryRule === null ? 1 : (int)$ctryRule->enabled;
                            $ctryTooltip = $ctryEnabled ? Yii::t('backend', 'tooltip_allow') : Yii::t('backend', 'tooltip_block');
                        ?>
                        <div class="card mb-2 country-block" data-country="<?= Html::encode($code) ?>" data-continent="<?= Html::encode($contKey) ?>">
                            <div class="card-header d-flex align-items-center flex-wrap gap-2">
                                <div class="form-check form-switch mb-0" title="<?= $ctryTooltip ?>">
                                    <input class="form-check-input" type="checkbox" data-toggle-type="country" data-value="<?= Html::encode($code) ?>" <?= $ctryEnabled ? 'checked' : '' ?>>
                                </div>
                                <b><?= Html::encode($ctryName) ?></b>
                                <span class="badge bg-info"><?= Html::encode($code) ?></span>
                                <span class="badge bg-secondary"><?= (int)$c['asn_count'] ?> ASN</span>
                                <?php if (!empty($c['blocked_count'])): ?>
                                    <span class="badge bg-danger" title="<?= Yii::t('backend', 'Заблокировано ASN') ?>">
                                        <?= Yii::t('backend', 'Заблокировано:') ?> <?= $c['blocked_count'] ?>
                                    </span>
                                <?php endif; ?>
                                <button type="button" class="btn btn-sm btn-outline-primary ms-auto btn-expand-country" data-code="<?= Html::encode($code) ?>"><?= Yii::t('backend', 'Показать ASN') ?></button>
                            </div>
                            <div class="card-body country-asns" style="display:none;" data-code="<?= Html::encode($code) ?>" data-loaded="0"></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div id="search-results" style="display:none;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0" id="search-title"><?= Yii::t('backend', 'Результаты поиска') ?></h5>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-clear-search"><?= Yii::t('backend', 'Закрыть') ?></button>
        </div>
        <div id="search-results-list"></div>
    </div>
</div>

<?php
$this->registerCss(<<<CSS
.asn-filter-page .card-header { background: #f8f9fa; }
.asn-filter-page .country-block { border-left: 3px solid #0d6efd33; }
.asn-filter-page .asn-row { padding: .35rem .75rem; border-bottom: 1px solid #eee; display: flex; align-items: center; gap: .75rem; }
.asn-filter-page .asn-row:last-child { border-bottom: 0; }
.asn-filter-page .asn-row .asn-badge { font-family: monospace; min-width: 70px; }
.asn-filter-page .asn-row .asn-desc { flex: 1; }
CSS);

$this->registerJs(<<<JS
(function () {
    'use strict';

    var CSRF = window.__ASN_CSRF || { name: '', token: '' };
    var URLS = window.__ASN_URLS || {};
    var LANG = window.__ASN_LANG || {};
    var ADMIN_ASN = parseInt(window.__ADMIN_ASN, 10) || 0;
    window.__CURRENT_VIEW = 'tree'; // tree | search | blocked

    function postJson(url, data) {
        var body = new URLSearchParams();
        Object.keys(data).forEach(function (k) { body.set(k, data[k]); });
        if (CSRF.name) body.set(CSRF.name, CSRF.token);
        return fetch(url, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': CSRF.token }, body: body.toString() })
            .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); });
    }

    function getJson(url, params) {
        var qs = new URLSearchParams(params).toString();
        var sep = url.indexOf('?') === -1 ? '?' : '&';
        return fetch(url + sep + qs, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); });
    }

    // --- SEO Боты: кнопка Настройки/Закрыть ---
    var btnToggleBots = document.getElementById('btn-toggle-bot-settings');
    var botsSettings = document.getElementById('bots-settings');
    
    if (btnToggleBots && botsSettings) {
        btnToggleBots.addEventListener('click', function() {
            var isHidden = botsSettings.style.display === 'none';
            botsSettings.style.display = isHidden ? 'block' : 'none';
            btnToggleBots.textContent = isHidden ? LANG.close : LANG.settings;
        });
    }

    // --- SEO Боты: главный тумблер ---
    var botsEnabledEl = document.getElementById('bots-enabled');
    if (botsEnabledEl) {
        botsEnabledEl.addEventListener('change', function(e) {
            var isChecked = e.target.checked;
            postJson(URLS.setSearchBots, { param: 'asn_filter.search_bots_enabled', value: isChecked ? 1 : 0 })
            .catch(function(err) {
                alert(LANG.save_error + ': ' + err.message);
                e.target.checked = !isChecked;
            });
        });
    }

    // --- SEO Боты: точечные тумблеры ---
    document.querySelectorAll('.bot-toggle').forEach(function(el) {
        el.addEventListener('change', function(e) {
            var param = e.target.dataset.param;
            var isChecked = e.target.checked;
            postJson(URLS.setSearchBots, { param: param, value: isChecked ? 1 : 0 })
            .catch(function(err) {
                alert(LANG.save_error + ': ' + err.message);
                e.target.checked = !isChecked;
            });
        });
    });

    // --- Визуализация и рубильник ---
    function updateVisualState(el) {
        var type = el.dataset.toggleType;
        var isChecked = el.checked;
        var opacity = isChecked ? '1' : '0.5';

        if (type === 'continent') {
            var block = el.closest('.continent-block');
            if (block) {
                block.querySelectorAll('.country-block').forEach(function(ctry) {
                    ctry.style.opacity = opacity;
                    ctry.querySelector('.card-body').style.opacity = opacity;
                });
            }
        } else if (type === 'country') {
            var cblock = el.closest('.country-block');
            if (cblock) {
                cblock.querySelector('.country-asns').style.opacity = opacity;
            }
        }
    }

    document.querySelectorAll('[data-toggle-type="continent"]').forEach(function(el) { if(!el.checked) updateVisualState(el); });
    document.querySelectorAll('[data-toggle-type="country"]').forEach(function(el) { if(!el.checked) updateVisualState(el); });

    document.getElementById('global-toggle').addEventListener('change', function (e) {
        var el = e.target;
        el.disabled = true;
        postJson(URLS.setGlobal, { value: el.checked ? 1 : 0 }).then(function (r) {
            el.disabled = false;
            if (r && r.ok) {
                document.getElementById('global-toggle-label').textContent = el.checked ? LANG.allow_whitelist : LANG.allow_all;
                var badge = document.getElementById('global-badge');
                badge.textContent = el.checked ? LANG.filter_on : LANG.filter_off;
                badge.className = 'badge fs-6 bg-' + (el.checked ? 'success' : 'danger');
            }
        }).catch(function (err) { 
            alert(LANG.save_error + ': ' + err.message);
            el.disabled = false; 
            el.checked = !el.checked; 
        });
    });

    var errorCodeEl = document.getElementById('error-code');
    var randomErrorEl = document.getElementById('random-error');
    [errorCodeEl, randomErrorEl].forEach(function(el) {
        el.addEventListener('change', function() {
            postJson(URLS.setErrorCode, { code: errorCodeEl.value, random: randomErrorEl.checked ? 1 : 0 })
            .catch(function(err) {
                alert(LANG.save_error + ': ' + err.message);
            });
        });
    });

    document.addEventListener('change', function (e) {
        var el = e.target;
        if (!el.matches('[data-toggle-type]')) return;
        var type = el.dataset.toggleType;
        var value = el.dataset.value || '';
        var enabled = el.checked ? 1 : 0;
        var url, data;

        if (type === 'continent') { url = URLS.toggleContinent; data = { value: value, enabled: enabled }; }
        else if (type === 'country') { url = URLS.toggleCountry; data = { value: value, enabled: enabled }; }
        else if (type === 'asn') { url = URLS.toggleAsn; data = { asn: value, enabled: enabled }; }
        else { return; }

        el.disabled = true;
        var wrapper = el.closest('.form-check');
        if (wrapper) wrapper.title = enabled ? LANG.tooltip_allow : LANG.tooltip_block;

        postJson(url, data).then(function (r) {
            el.disabled = false;
            if (r && r.ok) { 
                updateVisualState(el);
                
                if (type === 'asn') {
                    var blockedEl = document.getElementById('summary-blocked');
                    if (blockedEl) {
                        var current = parseInt(blockedEl.textContent, 10) || 0;
                        current = enabled ? current - 1 : current + 1;
                        blockedEl.textContent = Math.max(0, current);
                    }
                    
                    if (window.__CURRENT_VIEW === 'blocked' && enabled) {
                        var row = el.closest('.asn-row');
                        if (row) {
                            row.style.transition = 'all 0.3s ease';
                            row.style.opacity = '0';
                            row.style.transform = 'translateX(20px)';
                            setTimeout(function() { row.remove(); }, 300);
                        }
                    }
                } else {
                    location.reload();
                }
            } else { 
                if (r && r.error) alert(r.error);
                el.checked = !el.checked; 
                if (wrapper) wrapper.title = !enabled ? LANG.tooltip_allow : LANG.tooltip_block;
            }
        }).catch(function (err) { 
            console.error(err); 
            el.disabled = false; 
            el.checked = !el.checked;
            alert(LANG.save_error + ': ' + err.message);
        });
    });

    document.getElementById('btn-show-blocked').addEventListener('click', function() {
        window.__CURRENT_VIEW = 'blocked';
        document.getElementById('asn-tree').style.display = 'none';
        var wrap = document.getElementById('search-results');
        var list = document.getElementById('search-results-list');
        wrap.style.display = 'block';
        document.getElementById('search-title').textContent = LANG.blocked_title;
        list.innerHTML = '<div class="text-muted">' + LANG.loading + '</div>';

        getJson(URLS.showBlocked, {}).then(function (r) {
            if (!r || !r.ok) { list.innerHTML = '<div class="text-danger">' + LANG.error_load + '</div>'; return; }
            var items = r.items || [];
            if (!items.length) { list.innerHTML = '<div class="text-muted p-3">' + LANG.no_records + '</div>'; return; }
            list.innerHTML = renderSearchRows(items);
        });
    });

    function renderSearchRows(items) {
        var html = '<div class="card"><div class="card-body p-0">';
        var isEn = document.documentElement.lang.substring(0, 2) === 'en';
        items.forEach(function (it) {
            var cName = isEn ? it.country_en : it.country_ru;
            var contName = isEn ? it.continent_en : it.continent_ru;
            var tooltip = it.enabled ? LANG.tooltip_allow : LANG.tooltip_block;
            var isAdmin = (ADMIN_ASN && it.asn === ADMIN_ASN);
            
            var toggleHtml = isAdmin 
                ? '<div class="form-check form-switch mb-0" title="' + LANG.protected_asn + '"><input class="form-check-input" type="checkbox" checked disabled></div>'
                : '<div class="form-check form-switch mb-0" title="' + tooltip + '"><input class="form-check-input" type="checkbox" data-toggle-type="asn" data-value="' + it.asn + '"' + (it.enabled ? ' checked' : '') + '></div>';
            
            var lockIcon = isAdmin ? ' <i class="fas fa-lock text-warning" title="' + LANG.protected_asn + '"></i>' : '';

            html += '<div class="asn-row" style="opacity:' + (it.enabled ? '1' : '0.6') + '">' +
                toggleHtml +
                '<span class="asn-badge badge bg-dark">AS' + it.asn + '</span>' +
                '<span class="asn-desc">' + escapeHtml(it.description) + lockIcon + '</span>' +
                '<span class="badge bg-info">' + escapeHtml(it.country_code) + '</span>' +
                '<span class="text-muted small">' + escapeHtml(cName) + ' / ' + escapeHtml(contName) + '</span>' +
                '</div>';
        });
        html += '</div></div>';
        return html;
    }

    function renderAsnRows(items) {
        if (!items.length) return '<div class="text-muted small p-2">' + LANG.no_records + '</div>';
        var html = '';
        items.forEach(function (it) {
            var tooltip = it.enabled ? LANG.tooltip_allow : LANG.tooltip_block;
            var isAdmin = (ADMIN_ASN && it.asn === ADMIN_ASN);

            var toggleHtml = isAdmin 
                ? '<div class="form-check form-switch mb-0" title="' + LANG.protected_asn + '"><input class="form-check-input" type="checkbox" checked disabled></div>'
                : '<div class="form-check form-switch mb-0" title="' + tooltip + '"><input class="form-check-input" type="checkbox" data-toggle-type="asn" data-value="' + it.asn + '"' + (it.enabled ? ' checked' : '') + '></div>';
            
            var lockIcon = isAdmin ? ' <i class="fas fa-lock text-warning" title="' + LANG.protected_asn + '"></i>' : '';

            html += '<div class="asn-row">' +
                toggleHtml +
                '<span class="asn-badge badge bg-dark">AS' + it.asn + '</span>' +
                '<span class="asn-desc">' + escapeHtml(it.description) + lockIcon + '</span>' +
                '</div>';
        });
        return html;
    }

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-expand-continent');
        if (btn) {
            var body = btn.closest('.continent-block').querySelector('.continent-countries');
            var open = body.style.display === 'none';
            body.style.display = open ? 'block' : 'none';
            btn.textContent = open ? LANG.hide_countries : LANG.show_countries;
            return;
        }

        var btnC = e.target.closest('.btn-expand-country');
        if (btnC) {
            var cbody = btnC.closest('.country-block').querySelector('.country-asns');
            var openC = cbody.style.display === 'none';
            cbody.style.display = openC ? 'block' : 'none';
            btnC.textContent = openC ? LANG.hide_asn : LANG.show_asn;
            if (openC && cbody.dataset.loaded === '0') {
                cbody.dataset.loaded = '1';
                cbody.innerHTML = '<div class="text-muted small">' + LANG.loading + '</div>';
                getJson(URLS.loadCountryAsns, { code: cbody.dataset.code }).then(function (r) {
                    cbody.innerHTML = renderAsnRows(r.items || []);
                });
            }
            return;
        }

        if (e.target.id === 'btn-clear-search') {
            window.__CURRENT_VIEW = 'tree';
            document.getElementById('filter-search').value = '';
            document.getElementById('search-results').style.display = 'none';
            document.getElementById('asn-tree').style.display = 'block';
            document.getElementById('search-title').textContent = LANG.search_results;
        }
    });

    var searchTimer;
    document.getElementById('filter-search').addEventListener('input', function () {
        clearTimeout(searchTimer);
        var q = this.value.trim();
        searchTimer = setTimeout(function () {
            if (q.length < 2) {
                window.__CURRENT_VIEW = 'tree';
                document.getElementById('search-results').style.display = 'none';
                document.getElementById('asn-tree').style.display = 'block';
                return;
            }
            window.__CURRENT_VIEW = 'search';
            document.getElementById('asn-tree').style.display = 'none';
            var wrap = document.getElementById('search-results');
            wrap.style.display = 'block';
            document.getElementById('search-title').textContent = LANG.search_results;
            var list = document.getElementById('search-results-list');
            list.innerHTML = '<div class="text-muted">' + LANG.searching + '</div>';

            getJson(URLS.search, { q: q }).then(function (r) {
                list.innerHTML = (r.items && r.items.length) ? renderSearchRows(r.items) : '<div class="text-muted p-3">' + LANG.not_found + '</div>';
            });
        }, 300);
    });

    function escapeHtml(s) {
        return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }
})();
JS, \yii\web\View::POS_END, 'asn-whitelist-init');