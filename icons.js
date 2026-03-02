// Icons8 3D Fluency icon map
// CDN format: https://img.icons8.com/3d-fluency/{size}/{name}.png

const ICON_BASE = 'https://img.icons8.com/3d-fluency';

const CATEGORY_ICONS = {
    'Essentials':          `${ICON_BASE}/80/passport.png`,
    'Toiletries':          `${ICON_BASE}/80/cosmetics.png`,
    'Clothing':            `${ICON_BASE}/80/t-shirt.png`,
    'Shoes':               `${ICON_BASE}/80/sneakers.png`,
    'Electronics':         `${ICON_BASE}/80/laptop.png`,
    'Business Trip':       `${ICON_BASE}/80/briefcase.png`,
    'Gym':                 `${ICON_BASE}/80/dumbbell.png`,
    'Beach':               `${ICON_BASE}/80/beach.png`,
    'Swimming':            `${ICON_BASE}/80/swimmer.png`,
    'Snow Sports':         `${ICON_BASE}/80/skiing.png`,
    'Hiking':              `${ICON_BASE}/80/trekking.png`,
    'Camping':             `${ICON_BASE}/80/camping-tent.png`,
    'Rainy Weather':       `${ICON_BASE}/80/rain.png`,
    'Hot & Sunny Weather': `${ICON_BASE}/80/sun.png`,
    'Snowy Weather':       `${ICON_BASE}/80/snowflake.png`,
    'Windy Weather':       `${ICON_BASE}/80/wind.png`,
    'Cold Weather':        `${ICON_BASE}/80/cold.png`,
};

const ITEM_ICONS = {
    // Essentials
    'Passport':           `${ICON_BASE}/40/passport.png`,
    'ID':                 `${ICON_BASE}/40/identification-documents.png`,
    'Cash':               `${ICON_BASE}/40/money.png`,
    'Credit Card':        `${ICON_BASE}/40/bank-card-back-side.png`,
    'Phone':              `${ICON_BASE}/40/iphone.png`,
    'Charger':            `${ICON_BASE}/40/charging.png`,
    'Headphones':         `${ICON_BASE}/40/headphones.png`,
    'Power Bank':         `${ICON_BASE}/40/power-bank.png`,
    'Travel Adapter':     `${ICON_BASE}/40/electrical.png`,
    'Keys':               `${ICON_BASE}/40/key.png`,

    // Toiletries
    'Toothbrush':         `${ICON_BASE}/40/toothbrush.png`,
    'Toothpaste':         `${ICON_BASE}/40/toothpaste.png`,
    'Shampoo':            `${ICON_BASE}/40/shampoo.png`,
    'Conditioner':        `${ICON_BASE}/40/conditioner.png`,
    'Body Wash':          `${ICON_BASE}/40/soap.png`,
    'Deodorant':          `${ICON_BASE}/40/deodorant.png`,
    'Razor':              `${ICON_BASE}/40/razor.png`,
    'Moisturizer':        `${ICON_BASE}/40/face-cream.png`,
    'Sunscreen':          `${ICON_BASE}/40/sunscreen.png`,
    'Lip Balm':           `${ICON_BASE}/40/lip-balm.png`,
    'Hair Brush':         `${ICON_BASE}/40/hair-brush.png`,

    // Clothing
    'T-Shirts':           `${ICON_BASE}/40/t-shirt.png`,
    'Tank Tops':          `${ICON_BASE}/40/sleeveless-shirt.png`,
    'Long Sleeve Shirts': `${ICON_BASE}/40/long-sleeve-shirt.png`,
    'Button-Down Shirts': `${ICON_BASE}/40/shirt.png`,
    'Polo Shirts':        `${ICON_BASE}/40/polo-shirt.png`,
    'Blouse':             `${ICON_BASE}/40/blouse.png`,
    'Hoodie':             `${ICON_BASE}/40/hoodie.png`,
    'Cardigan':           `${ICON_BASE}/40/cardigan.png`,
    'Sweater':            `${ICON_BASE}/40/sweater.png`,
    'Vest':               `${ICON_BASE}/40/vest.png`,
    'Jeans':              `${ICON_BASE}/40/jeans.png`,
    'Pants':              `${ICON_BASE}/40/trousers.png`,
    'Chinos':             `${ICON_BASE}/40/trousers.png`,
    'Sweatpants':         `${ICON_BASE}/40/sweatpants.png`,
    'Leggings':           `${ICON_BASE}/40/leggings.png`,
    'Shorts':             `${ICON_BASE}/40/shorts.png`,
    'Skirt':              `${ICON_BASE}/40/skirt.png`,
    'Dress':              `${ICON_BASE}/40/dress.png`,
    'Jacket':             `${ICON_BASE}/40/jacket.png`,
    'Rain Jacket':        `${ICON_BASE}/40/raincoat.png`,
    'Coat':               `${ICON_BASE}/40/coat.png`,
    'Underwear':          `${ICON_BASE}/40/underwear.png`,
    'Sports Bra':         `${ICON_BASE}/40/sports-bra.png`,
    'Socks':              `${ICON_BASE}/40/socks.png`,
    'No-Show Socks':      `${ICON_BASE}/40/socks.png`,
    'Compression Socks':  `${ICON_BASE}/40/socks.png`,
    'Thermal Underwear':  `${ICON_BASE}/40/thermal-underwear.png`,
    'Pajamas':            `${ICON_BASE}/40/pajamas.png`,
    'Scarf':              `${ICON_BASE}/40/scarf.png`,
    'Gloves':             `${ICON_BASE}/40/winter-gloves.png`,
    'Beanie':             `${ICON_BASE}/40/winter-hat.png`,

    // Shoes
    'Sneakers':           `${ICON_BASE}/40/sneakers.png`,
    'Running Shoes':      `${ICON_BASE}/40/running-shoes.png`,
    'Sandals':            `${ICON_BASE}/40/sandals.png`,
    'Flip Flops':         `${ICON_BASE}/40/flip-flops.png`,
    'Loafers':            `${ICON_BASE}/40/loafers.png`,
    'Boots':              `${ICON_BASE}/40/boots.png`,
    'Heels':              `${ICON_BASE}/40/high-heeled-shoe.png`,

    // Electronics
    'Laptop':             `${ICON_BASE}/40/laptop.png`,
    'Laptop Charger':     `${ICON_BASE}/40/charging.png`,
    'USB Drive':          `${ICON_BASE}/40/usb.png`,
    'Camera':             `${ICON_BASE}/40/camera.png`,

    // Business Trip
    'Dress Shirts':       `${ICON_BASE}/40/shirt.png`,
    'Dress Pants':        `${ICON_BASE}/40/trousers.png`,
    'Blazer':             `${ICON_BASE}/40/blazer.png`,
    'Dress Shoes':        `${ICON_BASE}/40/oxford-shoes.png`,
    'Tie':                `${ICON_BASE}/40/tie.png`,
    'Business Cards':     `${ICON_BASE}/40/business-card.png`,
    'Notebook':           `${ICON_BASE}/40/notebook.png`,

    // Gym
    'Workout Clothes':    `${ICON_BASE}/40/gym-shorts.png`,
    'Sports Shoes':       `${ICON_BASE}/40/running-shoes.png`,
    'Gym Towel':          `${ICON_BASE}/40/towel.png`,
    'Water Bottle':       `${ICON_BASE}/40/water-bottle.png`,

    // Beach
    'Swimsuit':           `${ICON_BASE}/40/swimsuit.png`,
    'Beach Towel':        `${ICON_BASE}/40/beach-towel.png`,
    'Sunglasses':         `${ICON_BASE}/40/sunglasses.png`,
    'Sun Hat':            `${ICON_BASE}/40/straw-hat.png`,
    'Beach Bag':          `${ICON_BASE}/40/beach-bag.png`,

    // Swimming
    'Goggles':            `${ICON_BASE}/40/goggles.png`,
    'Swim Cap':           `${ICON_BASE}/40/swimming-cap.png`,
    'Towel':              `${ICON_BASE}/40/towel.png`,

    // Snow Sports
    'Thermal Base Layer': `${ICON_BASE}/40/thermal-underwear.png`,
    'Ski Jacket':         `${ICON_BASE}/40/ski-jacket.png`,
    'Ski Pants':          `${ICON_BASE}/40/ski-pants.png`,
    'Wool Socks':         `${ICON_BASE}/40/socks.png`,
    'Ski Boots':          `${ICON_BASE}/40/ski-boot.png`,

    // Hiking
    'Hiking Boots':       `${ICON_BASE}/40/trekking-boots.png`,
    'Hiking Socks':       `${ICON_BASE}/40/socks.png`,
    'Backpack':           `${ICON_BASE}/40/backpack.png`,
    'Insect Repellent':   `${ICON_BASE}/40/insect-repellent.png`,
    'Snacks':             `${ICON_BASE}/40/energy-bar.png`,

    // Camping
    'Tent':               `${ICON_BASE}/40/camping-tent.png`,
    'Sleeping Bag':       `${ICON_BASE}/40/sleeping-bag.png`,
    'Sleeping Pad':       `${ICON_BASE}/40/yoga-mat.png`,
    'Camp Stove':         `${ICON_BASE}/40/camping-stove.png`,
    'Food':               `${ICON_BASE}/40/meal.png`,

    // Rainy Weather
    'Umbrella':                  `${ICON_BASE}/40/umbrella.png`,
    'Rain Poncho':               `${ICON_BASE}/40/raincoat.png`,
    'Waterproof Backpack Cover': `${ICON_BASE}/40/backpack.png`,

    // Hot & Sunny Weather
    'Sunscreen (SPF 50+)':   `${ICON_BASE}/40/sunscreen.png`,
    'Reusable Water Bottle': `${ICON_BASE}/40/water-bottle.png`,

    // Snowy Weather
    'Snow Boots':         `${ICON_BASE}/40/snow-boots.png`,
    'Hand Warmers':       `${ICON_BASE}/40/gloves.png`,
    'Heavy Puffer Jacket':`${ICON_BASE}/40/winter-jacket.png`,
    'Thermal Socks':      `${ICON_BASE}/40/socks.png`,

    // Windy Weather
    'Windbreaker Jacket': `${ICON_BASE}/40/windbreaker.png`,
    'Hair Ties':          `${ICON_BASE}/40/hair-accessories.png`,
    'Beanie / Ear Muffs': `${ICON_BASE}/40/winter-hat.png`,

    // Cold Weather
    'Warm Boots':         `${ICON_BASE}/40/boots.png`,
    'Heavy Coat':         `${ICON_BASE}/40/winter-jacket.png`,
    'Fleece Sweater':     `${ICON_BASE}/40/sweater.png`,
};

// Inline SVG placeholder shown when a CDN icon fails to load
const FALLBACK_SVG = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Crect x='2' y='2' width='20' height='20' rx='5' fill='%23dde6ee'/%3E%3Cpath d='M7 12h10M12 7v10' stroke='%2399a8b4' stroke-width='2' stroke-linecap='round'/%3E%3C/svg%3E";

function getCategoryIcon(category, size = 36) {
    const url = CATEGORY_ICONS[category] || FALLBACK_SVG;
    return `<img src="${url}" width="${size}" height="${size}" alt="${category}" style="object-fit:contain;flex-shrink:0;" onerror="this.src='${FALLBACK_SVG}';this.onerror=null;">`;
}

function getItemIcon(itemName, size = 24) {
    const url = ITEM_ICONS[itemName] || FALLBACK_SVG;
    return `<img src="${url}" width="${size}" height="${size}" alt="${itemName}" style="object-fit:contain;flex-shrink:0;" onerror="this.src='${FALLBACK_SVG}';this.onerror=null;">`;
}
