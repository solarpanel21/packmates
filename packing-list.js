// ═══════════════════════════════════════════════════════════════
//  ITEM DATABASE — sourced from List_database_packmate.xlsx
//  profile: { hot, cold, snowy, rainy, windy, beach, ski,
//             hiking, camping, swimming, gym, business }
// ═══════════════════════════════════════════════════════════════
const ITEM_DB = {
    'Essentials': [
        { name: 'Passport',       suggest: p => true },
        { name: 'ID',             suggest: p => true },
        { name: 'Cash',           suggest: p => true },
        { name: 'Credit Card',    suggest: p => true },
        { name: 'Phone',          suggest: p => true },
        { name: 'Charger',        suggest: p => true },
        { name: 'Headphones',     suggest: p => true },
        { name: 'Power Bank',     suggest: p => true },
        { name: 'Travel Adapter', suggest: p => true },
        { name: 'Keys',           suggest: p => true },
    ],
    'Toiletries': [
        { name: 'Toothbrush',  suggest: p => true },
        { name: 'Toothpaste',  suggest: p => true },
        { name: 'Shampoo',     suggest: p => true },
        { name: 'Conditioner', suggest: p => true },
        { name: 'Body Wash',   suggest: p => true },
        { name: 'Deodorant',   suggest: p => true },
        { name: 'Razor',       suggest: p => true },
        { name: 'Moisturizer', suggest: p => true },
        { name: 'Sunscreen',   suggest: p => p.hot || p.beach || p.hiking || p.camping },
        { name: 'Lip Balm',    suggest: p => p.cold || p.snowy || p.windy || p.ski },
        { name: 'Hair Brush',  suggest: p => true },
    ],
    'Clothing': [
        { name: 'T-Shirts',           suggest: p => true },
        { name: 'Tank Tops',          suggest: p => p.hot || p.beach || p.gym },
        { name: 'Long Sleeve Shirts', suggest: p => !p.hot || p.hiking || p.camping },
        { name: 'Button-Down Shirts', suggest: p => p.business },
        { name: 'Polo Shirts',        suggest: p => p.business || (!p.beach && !p.hot) },
        { name: 'Blouse',             suggest: p => p.business || p.hot },
        { name: 'Hoodie',             suggest: p => !p.hot && !p.beach },
        { name: 'Cardigan',           suggest: p => !p.hot || p.business },
        { name: 'Sweater',            suggest: p => p.cold || p.snowy },
        { name: 'Vest',               suggest: p => p.cold || p.hiking },
        { name: 'Jeans',              suggest: p => !p.hot && !p.beach },
        { name: 'Pants',              suggest: p => !p.beach || p.business || p.hiking },
        { name: 'Chinos',             suggest: p => p.business || (!p.hot && !p.beach) },
        { name: 'Sweatpants',         suggest: p => p.gym || p.camping || (!p.business && !p.beach) },
        { name: 'Leggings',           suggest: p => p.gym || p.hiking || p.cold },
        { name: 'Shorts',             suggest: p => p.hot || p.beach || p.gym },
        { name: 'Skirt',              suggest: p => p.hot || p.beach || (p.business && !p.cold) },
        { name: 'Dress',              suggest: p => p.hot || p.beach || (p.business && !p.cold) },
        { name: 'Jacket',             suggest: p => !p.hot && !p.beach },
        { name: 'Rain Jacket',        suggest: p => p.rainy || p.hiking || p.camping },
        { name: 'Coat',               suggest: p => p.cold || p.snowy },
        { name: 'Underwear',          suggest: p => true },
        { name: 'Sports Bra',         suggest: p => p.gym || p.beach || p.hiking || p.swimming },
        { name: 'Socks',              suggest: p => true },
        { name: 'No-Show Socks',      suggest: p => p.hot || p.business },
        { name: 'Compression Socks',  suggest: p => p.hiking || p.business },
        { name: 'Thermal Underwear',  suggest: p => p.cold || p.snowy || p.ski },
        { name: 'Pajamas',            suggest: p => true },
        { name: 'Scarf',              suggest: p => p.cold || p.snowy },
        { name: 'Gloves',             suggest: p => p.cold || p.snowy || p.ski },
        { name: 'Beanie',             suggest: p => p.cold || p.snowy || p.ski },
    ],
    'Shoes': [
        { name: 'Sneakers',      suggest: p => true },
        { name: 'Running Shoes', suggest: p => p.gym || p.hiking },
        { name: 'Sandals',       suggest: p => p.hot || p.beach },
        { name: 'Flip Flops',    suggest: p => p.beach || p.swimming },
        { name: 'Loafers',       suggest: p => p.business || (!p.hiking && !p.beach && !p.cold) },
        { name: 'Boots',         suggest: p => p.cold || p.hiking },
        { name: 'Heels',         suggest: p => p.business && !p.cold },
    ],
    'Electronics': [
        { name: 'Laptop',         suggest: p => p.business },
        { name: 'Laptop Charger', suggest: p => p.business },
        { name: 'Power Bank',     suggest: p => true },
        { name: 'USB Drive',      suggest: p => p.business },
        { name: 'Camera',         suggest: p => !p.business },
    ],
    'Business Trip': [
        { name: 'Dress Shirts',   suggest: p => p.business },
        { name: 'Dress Pants',    suggest: p => p.business },
        { name: 'Blazer',         suggest: p => p.business },
        { name: 'Dress Shoes',    suggest: p => p.business },
        { name: 'Tie',            suggest: p => p.business },
        { name: 'Business Cards', suggest: p => p.business },
        { name: 'Notebook',       suggest: p => p.business },
    ],
    'Gym': [
        { name: 'Workout Clothes', suggest: p => p.gym },
        { name: 'Sports Shoes',    suggest: p => p.gym },
        { name: 'Gym Towel',       suggest: p => p.gym },
        { name: 'Water Bottle',    suggest: p => p.gym || p.hiking || p.hot },
    ],
    'Beach': [
        { name: 'Swimsuit',    suggest: p => p.beach },
        { name: 'Beach Towel', suggest: p => p.beach },
        { name: 'Sunglasses',  suggest: p => p.beach || p.hot },
        { name: 'Sun Hat',     suggest: p => p.beach || p.hot },
        { name: 'Sandals',     suggest: p => p.beach || p.hot },
        { name: 'Sunscreen',   suggest: p => p.beach || p.hot },
        { name: 'Beach Bag',   suggest: p => p.beach },
    ],
    'Swimming': [
        { name: 'Swimsuit',   suggest: p => p.swimming },
        { name: 'Goggles',    suggest: p => p.swimming },
        { name: 'Swim Cap',   suggest: p => p.swimming },
        { name: 'Towel',      suggest: p => p.swimming || p.beach },
        { name: 'Flip Flops', suggest: p => p.swimming || p.beach },
    ],
    'Snow Sports': [
        { name: 'Thermal Base Layer', suggest: p => p.ski },
        { name: 'Ski Jacket',         suggest: p => p.ski },
        { name: 'Ski Pants',          suggest: p => p.ski },
        { name: 'Gloves',             suggest: p => p.ski || p.cold },
        { name: 'Beanie',             suggest: p => p.ski || p.cold },
        { name: 'Goggles',            suggest: p => p.ski },
        { name: 'Wool Socks',         suggest: p => p.ski || p.cold },
        { name: 'Ski Boots',          suggest: p => p.ski },
    ],
    'Hiking': [
        { name: 'Hiking Boots',    suggest: p => p.hiking },
        { name: 'Hiking Socks',    suggest: p => p.hiking },
        { name: 'Backpack',        suggest: p => p.hiking || p.camping },
        { name: 'Rain Jacket',     suggest: p => p.hiking && p.rainy },
        { name: 'Sunscreen',       suggest: p => p.hiking },
        { name: 'Insect Repellent',suggest: p => p.hiking || p.camping },
        { name: 'Water Bottle',    suggest: p => p.hiking },
        { name: 'Snacks',          suggest: p => p.hiking || p.camping },
    ],
    'Camping': [
        { name: 'Tent',            suggest: p => p.camping },
        { name: 'Sleeping Bag',    suggest: p => p.camping },
        { name: 'Sleeping Pad',    suggest: p => p.camping },
        { name: 'Camp Stove',      suggest: p => p.camping },
        { name: 'Food',            suggest: p => p.camping },
        { name: 'Insect Repellent',suggest: p => p.camping },
    ],
    'Rainy Weather': [
        { name: 'Umbrella',                  suggest: p => p.rainy },
        { name: 'Rain Jacket',               suggest: p => p.rainy },
        { name: 'Rain Poncho',               suggest: p => p.rainy },
        { name: 'Waterproof Backpack Cover', suggest: p => p.rainy || p.hiking },
    ],
    'Hot & Sunny Weather': [
        { name: 'Sunscreen (SPF 50+)',   suggest: p => p.hot || p.beach },
        { name: 'Sunglasses',            suggest: p => p.hot || p.beach },
        { name: 'Sun Hat',               suggest: p => p.hot || p.beach },
        { name: 'Reusable Water Bottle', suggest: p => p.hot || p.hiking },
    ],
    'Snowy Weather': [
        { name: 'Thermal Socks',      suggest: p => p.snowy || p.cold },
        { name: 'Gloves',             suggest: p => p.snowy || p.cold },
        { name: 'Scarf',              suggest: p => p.snowy || p.cold },
        { name: 'Snow Boots',         suggest: p => p.snowy },
        { name: 'Hand Warmers',       suggest: p => p.snowy || p.cold },
        { name: 'Thermal Underwear',  suggest: p => p.snowy || p.cold },
        { name: 'Heavy Puffer Jacket',suggest: p => p.snowy },
    ],
    'Windy Weather': [
        { name: 'Lip Balm',           suggest: p => p.windy || p.cold },
        { name: 'Windbreaker Jacket', suggest: p => p.windy },
        { name: 'Hair Ties',          suggest: p => p.windy },
        { name: 'Beanie / Ear Muffs', suggest: p => p.windy || p.cold },
    ],
    'Cold Weather': [
        { name: 'Warm Boots',        suggest: p => p.cold || p.snowy },
        { name: 'Gloves',            suggest: p => p.cold || p.snowy },
        { name: 'Scarf',             suggest: p => p.cold || p.snowy },
        { name: 'Heavy Coat',        suggest: p => p.cold || p.snowy },
        { name: 'Wool Socks',        suggest: p => p.cold || p.snowy },
        { name: 'Thermal Underwear', suggest: p => p.cold || p.snowy },
        { name: 'Fleece Sweater',    suggest: p => p.cold || p.snowy },
    ],
};

// ═══════════════════════════════════════════════════════════════
//  DESTINATION LISTS
// ═══════════════════════════════════════════════════════════════
const BEACH_DESTS    = ['miami','hawaii','bali','cancun','maldives','ibiza','florida','bahamas','caribbean','phuket','tulum','cabo','st. lucia','dominican','jamaica','aruba','barbados','key west','malibu','santa monica','san diego','costa rica','rio','kopaonik'];
const SKI_DESTS      = ['aspen','vail','whistler','verbier','zermatt','alps','jackson hole','tahoe','banff','park city','breckenridge','steamboat','telluride','killington','courchevel'];
const ALWAYS_COLD    = ['alaska','iceland','norway','sweden','finland','greenland','lapland','siberia','antarctica','faroe'];
const RAINY_DESTS    = ['london','amsterdam','portland','seattle','dublin','edinburgh','vancouver','brussels','glasgow','bergen','reykjavik'];
const HOT_DRY_DESTS  = ['las vegas','phoenix','dubai','doha','riyadh','cairo','marrakech','tucson','palm springs','abu dhabi'];
const WINDY_DESTS    = ['chicago','wellington','cape town','patagonia','scotland','ireland','netherlands','ireland','north sea'];
const FOUR_SEASON    = ['new york','nyc','chicago','boston','montreal','toronto','moscow','berlin','warsaw','denver','minneapolis','detroit','cleveland','buffalo','milwaukee','philadelphia','pittsburgh','cincinnati','columbus','indianapolis','kansas city','st. louis','omaha','baltimore','washington','paris','vienna','budapest','prague','milan','zurich','munich','frankfurt','amsterdam'];

// ═══════════════════════════════════════════════════════════════
//  TRIP PROFILE BUILDER
// ═══════════════════════════════════════════════════════════════
function buildTripProfile(tripData) {
    const dest  = (tripData.destination || '').toLowerCase();
    const month = tripData.fromDate ? new Date(tripData.fromDate).getMonth() + 1 : null;
    const sel   = tripData.activityCategories || [];

    const isBeachDest    = BEACH_DESTS.some(k => dest.includes(k));
    const isSkiDest      = SKI_DESTS.some(k => dest.includes(k));
    const isColdDest     = ALWAYS_COLD.some(k => dest.includes(k));
    const isRainyDest    = RAINY_DESTS.some(k => dest.includes(k));
    const isHotDryDest   = HOT_DRY_DESTS.some(k => dest.includes(k));
    const isWindyDest    = WINDY_DESTS.some(k => dest.includes(k));
    const isFourSeason   = FOUR_SEASON.some(c => dest.includes(c));

    // Start from destination character
    let hot   = isBeachDest || isHotDryDest;
    let cold  = isColdDest || isSkiDest;
    let snowy = isColdDest || isSkiDest;
    let rainy = isRainyDest;
    let windy = isWindyDest || isRainyDest;

    // Override with seasonal data for four-season cities
    if (month && isFourSeason && !isBeachDest && !isSkiDest) {
        hot   = [6, 7, 8].includes(month);
        cold  = [11, 12, 1, 2, 3].includes(month);
        snowy = [12, 1, 2].includes(month);
        rainy = [4, 5, 9, 10].includes(month);
        windy = rainy || cold;
    }

    // Activity flags (user chips + destination)
    const beach    = isBeachDest || sel.includes('Beach');
    const ski      = isSkiDest   || sel.includes('Snow Sports');
    const hiking   = sel.includes('Hiking');
    const camping  = sel.includes('Camping');
    const swimming = beach || sel.includes('Swimming');
    const gym      = sel.includes('Gym');
    const business = sel.includes('Business Trip');

    // Activities can modify weather profile
    if (beach)    hot   = true;
    if (ski)    { cold  = true; snowy = true; }

    return { hot, cold, snowy, rainy, windy, beach, ski, hiking, camping, swimming, gym, business };
}

// ═══════════════════════════════════════════════════════════════
//  STATE
// ═══════════════════════════════════════════════════════════════
const tripData = JSON.parse(localStorage.getItem('currentTrip') || '{}');
const profile  = buildTripProfile(tripData);

const destination = tripData.destination || 'Trip';
const fromDate    = tripData.fromDate    || '';
const toDate      = tripData.toDate      || '';
const travelers   = tripData.travelers   || 1;

function formatDate(d) {
    if (!d) return '';
    const [y, m, day] = d.split('-');
    return `${parseInt(m)}/${parseInt(day)}/${y}`;
}
function tripDays(from, to) {
    if (!from || !to) return null;
    return Math.max(1, Math.round((new Date(to) - new Date(from)) / 86400000) + 1);
}

const days = tripDays(fromDate, toDate);

function getItemKey(cat, name) { return `${cat}::${name}`; }

// itemState: { "Cat::Item": { packed: bool, qty: number } }
let itemState   = JSON.parse(localStorage.getItem('packingItemState') || '{}');
// dismissed: Set of keys the user deleted from suggestions
let dismissed   = new Set(JSON.parse(localStorage.getItem('packingDismissed') || '[]'));
// customItems: { category: [name, ...] }  — user-added
let customItems = JSON.parse(localStorage.getItem('packingCustomItems') || '{}');

function saveState() {
    localStorage.setItem('packingItemState',  JSON.stringify(itemState));
    localStorage.setItem('packingDismissed',  JSON.stringify([...dismissed]));
    localStorage.setItem('packingCustomItems',JSON.stringify(customItems));
    // Mirror for tripPreview panel
    const mirror = {};
    Object.entries(itemState).forEach(([k, v]) => { mirror[k] = v.packed; });
    localStorage.setItem('packingState', JSON.stringify(mirror));
}

// ── Header ──
document.getElementById('packingListTitle').textContent   = destination + ' – Packing List';
document.getElementById('packingSummaryTitle').textContent = destination;
const metaParts = [];
if (fromDate && toDate) metaParts.push(`${formatDate(fromDate)} – ${formatDate(toDate)}`);
if (days)               metaParts.push(`${days} day${days !== 1 ? 's' : ''}`);
metaParts.push(`${travelers} traveler${travelers !== 1 ? 's' : ''}`);
document.getElementById('packingSummaryMeta').textContent = metaParts.join(' · ');

// ── Profile badge ──
const profileBadges = [];
if (profile.hot)      profileBadges.push('☀️ Warm');
if (profile.cold)     profileBadges.push('🧊 Cold');
if (profile.snowy)    profileBadges.push('❄️ Snowy');
if (profile.rainy)    profileBadges.push('🌧️ Rainy');
if (profile.windy)    profileBadges.push('💨 Windy');
if (profile.beach)    profileBadges.push('🏖️ Beach');
if (profile.ski)      profileBadges.push('⛷️ Ski');
if (profile.hiking)   profileBadges.push('🥾 Hiking');
if (profile.camping)  profileBadges.push('⛺ Camping');
if (profile.swimming) profileBadges.push('🏊 Swimming');
if (profile.gym)      profileBadges.push('💪 Gym');
if (profile.business) profileBadges.push('💼 Business');

// ── Count helpers ──
function getSuggestedKeys() {
    const keys = [];
    Object.entries(ITEM_DB).forEach(([cat, items]) => {
        items.forEach(item => {
            if (item.suggest(profile) && !dismissed.has(getItemKey(cat, item.name)))
                keys.push(getItemKey(cat, item.name));
        });
    });
    // custom items always included
    Object.entries(customItems).forEach(([cat, names]) => {
        names.forEach(name => keys.push(getItemKey(cat, name)));
    });
    return keys;
}

function updateCounts() {
    const all    = getSuggestedKeys();
    const packed = all.filter(k => itemState[k]?.packed).length;
    const left   = all.length - packed;
    document.getElementById('packedCount').textContent   = `${packed}/${all.length} packed`;
    document.getElementById('unpackedCount').textContent = `${left} item${left !== 1 ? 's' : ''} left`;
}

// ── Render ──
let currentFilter  = 'all';
let showAllItems   = false;

function buildItemRow(cat, name, isCustom) {
    const key    = getItemKey(cat, name);
    const state  = itemState[key] || { packed: false, qty: 1 };
    const packed = state.packed;

    const article = document.createElement('article');
    article.className = `packing-item${packed ? ' packing-item--packed' : ''}`;
    article.dataset.status = packed ? 'packed' : 'not-packed';

    article.innerHTML = `
        <button class="packing-check${packed ? ' packing-check--checked' : ''}" aria-label="${packed ? 'Mark as not packed' : 'Mark as packed'}"></button>
        <div class="packing-item-content">
            <div class="packing-item-top">
                <h3 class="packing-item-name" style="${packed ? 'text-decoration:line-through;color:#99a8b4;' : ''}">${name}</h3>
                <div style="display:flex;align-items:center;gap:5px;">
                    <button class="qty-btn" data-key="${key}" data-delta="-1" style="width:18px;height:18px;border-radius:50%;border:1px solid #cdd6de;background:#f7fafc;cursor:pointer;font-size:0.75rem;display:inline-flex;align-items:center;justify-content:center;padding:0;flex-shrink:0;">−</button>
                    <span class="packing-quantity qty-display" data-key="${key}">x${state.qty}</span>
                    <button class="qty-btn" data-key="${key}" data-delta="1" style="width:18px;height:18px;border-radius:50%;border:1px solid #cdd6de;background:#f7fafc;cursor:pointer;font-size:0.75rem;display:inline-flex;align-items:center;justify-content:center;padding:0;flex-shrink:0;">+</button>
                    <button class="dismiss-btn" data-key="${key}" data-cat="${cat}" data-name="${name}" title="Remove suggestion" style="width:18px;height:18px;border-radius:50%;border:none;background:#fdecea;cursor:pointer;font-size:0.7rem;display:inline-flex;align-items:center;justify-content:center;padding:0;flex-shrink:0;color:#c0392b;">✕</button>
                </div>
            </div>
            <div class="packing-item-meta">
                <span class="packing-tag">${cat}</span>
                <span class="packing-status ${packed ? 'packing-status--packed' : 'packing-status--not-packed'}">${packed ? 'Packed' : 'Unpacked'}</span>
            </div>
        </div>`;

    // Toggle packed
    article.querySelector('.packing-check').addEventListener('click', () => {
        const cur = itemState[key] || { packed: false, qty: 1 };
        cur.packed = !cur.packed;
        itemState[key] = cur;
        saveState();
        renderList();
        updateCounts();
    });

    // Qty buttons
    article.querySelectorAll('.qty-btn').forEach(btn => {
        btn.addEventListener('click', e => {
            e.stopPropagation();
            const cur = itemState[btn.dataset.key] || { packed: false, qty: 1 };
            cur.qty = Math.max(1, (cur.qty || 1) + parseInt(btn.dataset.delta));
            itemState[btn.dataset.key] = cur;
            saveState();
            document.querySelectorAll(`.qty-display[data-key="${btn.dataset.key}"]`)
                .forEach(el => el.textContent = `x${cur.qty}`);
        });
    });

    // Dismiss (delete) button
    article.querySelector('.dismiss-btn').addEventListener('click', e => {
        e.stopPropagation();
        const k = e.currentTarget.dataset.key;
        dismissed.add(k);
        // Also remove from custom items if it was custom
        const cat2  = e.currentTarget.dataset.cat;
        const name2 = e.currentTarget.dataset.name;
        if (customItems[cat2]) {
            customItems[cat2] = customItems[cat2].filter(n => n !== name2);
            if (!customItems[cat2].length) delete customItems[cat2];
        }
        saveState();
        renderList();
        updateCounts();
    });

    return article;
}

function buildCategorySection(cat, itemEntries) {
    // itemEntries: [{ name, isSuggested }]
    const filtered = itemEntries.filter(({ name, isSuggested }) => {
        if (!isSuggested && !showAllItems) return false;
        const key    = getItemKey(cat, name);
        const packed = itemState[key]?.packed || false;
        if (currentFilter === 'packed')     return packed;
        if (currentFilter === 'not-packed') return !packed;
        return true;
    });
    if (!filtered.length) return null;

    const section = document.createElement('div');
    section.className = 'packing-category-section';

    const hasSuggested = filtered.some(e => e.isSuggested);
    section.innerHTML = `
        <div class="packing-category-header">
            ${hasSuggested ? '<span class="suggested-dot"></span>' : ''}
            ${cat}
            ${hasSuggested ? '<span class="packing-suggested-badge">✨ Suggested</span>' : ''}
        </div>`;

    filtered.forEach(({ name }) => {
        const row = buildItemRow(cat, name, false);
        section.appendChild(row);
    });
    return section;
}

function renderList() {
    const body = document.getElementById('packingListBody');
    body.innerHTML = '';

    // Profile badges banner
    if (profileBadges.length) {
        const banner = document.createElement('div');
        banner.className = 'packing-suggested-banner';
        banner.innerHTML = `<strong>✨ Adapted for your trip</strong>${profileBadges.join('  ')}`;
        body.appendChild(banner);
    }

    // Build per-category entries
    Object.entries(ITEM_DB).forEach(([cat, dbItems]) => {
        const entries = dbItems
            .filter(item => {
                const key = getItemKey(cat, item.name);
                const isSugg = item.suggest(profile) && !dismissed.has(key);
                return isSugg || showAllItems;
            })
            .map(item => ({
                name: item.name,
                isSuggested: item.suggest(profile) && !dismissed.has(getItemKey(cat, item.name))
            }));

        if (!entries.length) return;

        // For non-suggested items shown via "show all", mark them
        const section = buildCategorySection(cat, entries);
        if (section) body.appendChild(section);
    });

    // Custom-added items
    Object.entries(customItems).forEach(([cat, names]) => {
        if (!names.length) return;
        const section = document.createElement('div');
        section.className = 'packing-category-section';
        section.innerHTML = `<div class="packing-category-header">${cat} <span style="font-size:0.6rem;font-weight:500;color:#5f9d30;text-transform:none;letter-spacing:0;">custom</span></div>`;
        names.forEach(name => {
            const key    = getItemKey(cat, name);
            const packed = itemState[key]?.packed || false;
            if (currentFilter === 'packed'     && !packed) return;
            if (currentFilter === 'not-packed' && packed)  return;
            section.appendChild(buildItemRow(cat, name, true));
        });
        if (section.children.length > 1) body.appendChild(section);
    });

    // "Show all / Fewer items" toggle
    const toggle = document.createElement('button');
    toggle.style.cssText = 'margin:16px auto 4px;display:block;background:none;border:1px solid #cdd6de;border-radius:999px;padding:6px 18px;font-size:0.78rem;cursor:pointer;color:#4b5a66;font-family:inherit;';
    toggle.textContent = showAllItems ? '↑ Show suggested items only' : '↓ Browse all items';
    toggle.addEventListener('click', () => {
        showAllItems = !showAllItems;
        renderList();
    });
    body.appendChild(toggle);
}

// ── Filters ──
document.querySelectorAll('.packing-filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.packing-filter-btn').forEach(b => b.classList.remove('packing-filter-btn--active'));
        btn.classList.add('packing-filter-btn--active');
        currentFilter = btn.dataset.filter;
        renderList();
    });
});

// ── Add item modal ──
const modal = document.getElementById('addItemModal');
document.getElementById('openAddItemModal').addEventListener('click', () => {
    modal.classList.add('is-visible');
    modal.setAttribute('aria-hidden', 'false');
});
document.getElementById('closeAddItemModal').addEventListener('click', closeModal);
document.getElementById('cancelAddItem').addEventListener('click', e => { e.preventDefault(); closeModal(); });

function closeModal() {
    modal.classList.remove('is-visible');
    modal.setAttribute('aria-hidden', 'true');
}

document.getElementById('addItemForm').addEventListener('submit', e => {
    e.preventDefault();
    const name     = document.getElementById('newItemName').value.trim();
    const category = document.getElementById('newItemCategory').value;
    const qty      = parseInt(document.getElementById('newItemQty').value) || 1;
    if (!name) return;

    // Check if it's already in ITEM_DB and just dismissed — un-dismiss instead
    const key    = getItemKey(category, name);
    const inDB   = ITEM_DB[category]?.some(i => i.name === name);
    if (inDB) {
        dismissed.delete(key);   // restore suggestion
    } else {
        if (!customItems[category]) customItems[category] = [];
        if (!customItems[category].includes(name)) customItems[category].push(name);
    }
    itemState[key] = { packed: false, qty };
    saveState();
    renderList();
    updateCounts();

    document.getElementById('newItemName').value = '';
    document.getElementById('newItemQty').value  = 1;
    closeModal();
});

// ── Init ──
renderList();
updateCounts();
