<?php

namespace Database\Seeders;

use App\Enums\LinkType;
use App\Enums\ProductBadge;
use App\Enums\TagType;
use App\Models\Category;
use App\Models\HeroSlide;
use App\Models\Page;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Spatie\MediaLibrary\HasMedia;

class CatalogSeeder extends Seeder
{
    /** @var array<string, Category> */
    protected array $cat = [];

    /** @var array<string, Tag> */
    protected array $tag = [];

    public function run(): void
    {
        $this->seedCategories();
        $this->seedTags();
        $this->seedProducts();
        $this->seedHeroSlides();
        $this->seedPages();
        $this->seedSettings();
    }

    protected function makeCategory(string $key, array $name, ?string $parentKey = null): Category
    {
        $category = new Category(['name' => $name, 'is_active' => true]);

        if ($parentKey && isset($this->cat[$parentKey])) {
            $this->cat[$parentKey]->appendNode($category);
        } else {
            $category->save();
        }

        return $this->cat[$key] = $category;
    }

    protected function seedCategories(): void
    {
        // Level 1 — the three product families shown in "Shop by Categories"
        $this->makeCategory('cosmetics', ['en' => 'Cosmetics & Skincare', 'ar' => 'مستحضرات التجميل والعناية بالبشرة', 'fr' => 'Cosmétiques et soins de la peau']);
        $this->makeCategory('supplements', ['en' => 'Food Supplements', 'ar' => 'المكمّلات الغذائية', 'fr' => 'Compléments alimentaires']);
        $this->makeCategory('devices', ['en' => 'Medical Devices', 'ar' => 'الأجهزة الطبية', 'fr' => 'Dispositifs médicaux']);

        // Level 2 (under Cosmetics & Skincare)
        $this->makeCategory('cleansers', ['en' => 'Cleansers', 'ar' => 'المنظّفات', 'fr' => 'Nettoyants'], 'cosmetics');
        $this->makeCategory('facecare', ['en' => 'Face Care', 'ar' => 'العناية بالوجه', 'fr' => 'Soins du visage'], 'cosmetics');
        $this->makeCategory('suncare', ['en' => 'Sun Care', 'ar' => 'الحماية من الشمس', 'fr' => 'Protection solaire'], 'cosmetics');
        $this->makeCategory('bodycare', ['en' => 'Body Care', 'ar' => 'العناية بالجسم', 'fr' => 'Soins du corps'], 'cosmetics');

        $this->seedCategoryMedia();
    }

    protected function makeTag(string $key, TagType $type, array $name): void
    {
        $this->tag[$key] = Tag::create(['type' => $type, 'name' => $name, 'is_active' => true]);
    }

    protected function seedTags(): void
    {
        $this->makeTag('oily', TagType::SkinType, ['en' => 'Oily', 'ar' => 'دهنية', 'fr' => 'Grasse']);
        $this->makeTag('dry', TagType::SkinType, ['en' => 'Dry', 'ar' => 'جافة', 'fr' => 'Sèche']);
        $this->makeTag('combination', TagType::SkinType, ['en' => 'Combination', 'ar' => 'مختلطة', 'fr' => 'Mixte']);
        $this->makeTag('sensitive', TagType::SkinType, ['en' => 'Sensitive', 'ar' => 'حساسة', 'fr' => 'Sensible']);

        $this->makeTag('acne', TagType::Concern, ['en' => 'Acne', 'ar' => 'حب الشباب', 'fr' => 'Acné']);
        $this->makeTag('aging', TagType::Concern, ['en' => 'Anti-aging', 'ar' => 'مكافحة الشيخوخة', 'fr' => 'Anti-âge']);
        $this->makeTag('brightening', TagType::Concern, ['en' => 'Brightening', 'ar' => 'تفتيح البشرة', 'fr' => 'Éclat']);
        $this->makeTag('hydration', TagType::Concern, ['en' => 'Hydration', 'ar' => 'الترطيب', 'fr' => 'Hydratation']);

        $this->makeTag('vegan', TagType::Highlight, ['en' => 'Vegan', 'ar' => 'نباتي', 'fr' => 'Végan']);
        $this->makeTag('fragrance-free', TagType::Highlight, ['en' => 'Fragrance-free', 'ar' => 'خالٍ من العطور', 'fr' => 'Sans parfum']);
        $this->makeTag('derm-tested', TagType::Highlight, ['en' => 'Dermatologist-tested', 'ar' => 'مختبَر سريريًا', 'fr' => 'Testé dermatologiquement']);
    }

    protected function seedProducts(): void
    {
        foreach ($this->products() as $data) {
            $product = Product::create([
                'name' => $data['name'],
                'short_description' => $data['short'],
                'description' => $data['description'],
                'ingredients' => $data['ingredients'],
                'benefits' => $data['benefits'],
                'how_to_use' => $data['how_to_use'],
                'price' => $data['price'],
                'compare_at_price' => $data['compare_at'] ?? null,
                'currency' => 'USD',
                'is_active' => true,
                'is_featured' => $data['featured'] ?? false,
                'badges' => $data['badges'] ?? [],
                'primary_category_id' => $this->cat[$data['category']]->id,
            ]);

            $product->categories()->sync([$this->cat[$data['category']]->id]);
            $product->tags()->sync(collect($data['tags'])->map(fn ($k) => $this->tag[$k]->id)->all());

            foreach ($data['variants'] ?? [] as $i => $variant) {
                $product->variants()->create([
                    'name' => $variant['name'],
                    'sku' => $variant['sku'] ?? null,
                    'price' => $variant['price'] ?? null,
                    'sort_order' => $i,
                    'is_default' => $i === 0,
                    'is_active' => true,
                ]);
            }

            foreach ($data['images'] ?? [] as $image) {
                $this->attachMedia($product, 'gallery', $image, $product->getTranslation('name', 'en'));
            }
        }
    }

    protected function products(): array
    {
        return [
            [
                'category' => 'cleansers', 'price' => 15.00,
                'badges' => [ProductBadge::NewArrival->value],
                'tags' => ['oily', 'acne', 'derm-tested'],
                'name' => ['en' => 'Dermo Glow Facial Cleanser', 'ar' => 'ديرمو غلو غسول الوجه', 'fr' => 'Dermo Glow Nettoyant Visage'],
                'short' => ['en' => 'Exfoliating AHA/BHA foaming face wash.', 'ar' => 'غسول رغوي مقشّر بأحماض AHA وBHA.', 'fr' => 'Nettoyant moussant exfoliant AHA/BHA.'],
                'description' => ['en' => 'A foaming cleanser with glycolic and salicylic acids that exfoliates dead cells, deeply unclogs pores and purifies the skin for a clearer, brighter and more balanced complexion.', 'ar' => 'غسول رغوي بحمضي الجلايكوليك والساليسيليك يقشّر الخلايا الميتة وينظّف المسام بعمق وينقّي البشرة لإطلالة أكثر صفاءً وإشراقًا وتوازنًا.', 'fr' => 'Un nettoyant moussant à l’acide glycolique et salicylique qui exfolie les cellules mortes, désincruste les pores en profondeur et purifie la peau pour un teint plus net, éclatant et équilibré.'],
                'ingredients' => ['en' => 'Glycolic Acid (AHA), Salicylic Acid (BHA), Aloe Vera, Allantoin, Tea Tree Oil.', 'ar' => 'حمض الجلايكوليك (AHA)، حمض الساليسيليك (BHA)، الصبار، الألانتوين، زيت شجرة الشاي.', 'fr' => 'Acide glycolique (AHA), Acide salicylique (BHA), Aloe vera, Allantoïne, Huile d’arbre à thé.'],
                'benefits' => ['en' => ['Exfoliates dead skin cells', 'Reduces blemishes and acne marks', 'Purifies and calms inflammation', 'Regulates excess sebum'], 'ar' => ['يقشّر خلايا الجلد الميتة', 'يقلّل الشوائب وآثار حب الشباب', 'ينقّي البشرة ويهدّئ الالتهاب', 'ينظّم إفراز الدهون الزائدة'], 'fr' => ['Exfolie les cellules mortes', 'Réduit les imperfections et cicatrices d’acné', 'Purifie la peau et réduit l’inflammation', 'Régule l’excès de sébum']],
                'how_to_use' => ['en' => ['Apply to wet skin', 'Lather in circular motions for one minute', 'Rinse thoroughly with water'], 'ar' => ['ضعيه على بشرة مبللة', 'دلّكيه بحركات دائرية لمدة دقيقة', 'اشطفيه جيدًا بالماء'], 'fr' => ['Appliquer sur peau mouillée', 'Faire mousser par mouvements circulaires une minute', 'Rincer abondamment']],
                'variants' => [['name' => '150 ml', 'sku' => 'DG-CL-150']],
                'images' => ['cleanser.jpg', 'collection.jpg'],
            ],
            [
                'category' => 'facecare', 'price' => 28.00,
                'featured' => true, 'badges' => [ProductBadge::Bestseller->value],
                'tags' => ['brightening', 'hydration', 'derm-tested'],
                'name' => ['en' => 'Clarifying Day Cream', 'ar' => 'كريم التفتيح النهاري', 'fr' => 'Crème Clarifiante Jour'],
                'short' => ['en' => 'Brightening day moisturiser for an even tone.', 'ar' => 'مرطّب نهاري مفتّح لتوحيد لون البشرة.', 'fr' => 'Soin de jour clarifiant pour un teint unifié.'],
                'description' => ['en' => 'A daytime clarifying cream with niacinamide, hyaluronic acid, alpha arbutin and kojic acid that reduces dark spots, evens tone and delivers lasting hydration.', 'ar' => 'كريم نهاري مفتّح يحتوي على النياسيناميد وحمض الهيالورونيك وألفا أربوتين وحمض الكوجيك، يقلّل البقع الداكنة ويوحّد اللون ويمنح ترطيبًا يدوم طويلًا.', 'fr' => 'Une crème de jour clarifiante au niacinamide, acide hyaluronique, alpha arbutine et acide kojique qui atténue les taches, unifie le teint et apporte une hydratation durable.'],
                'ingredients' => ['en' => 'Niacinamide (Vitamin B3), Hyaluronic Acid, Alpha Arbutin, Kojic Acid.', 'ar' => 'نياسيناميد (فيتامين B3)، حمض الهيالورونيك، ألفا أربوتين، حمض الكوجيك.', 'fr' => 'Niacinamide (Vitamine B3), Acide hyaluronique, Alpha arbutine, Acide kojique.'],
                'benefits' => ['en' => ['Reduces pigmentation spots', 'Evens skin tone', 'Intense, long-lasting hydration', 'Fades hyperpigmentation and post-blemish marks'], 'ar' => ['يقلّل البقع التصبغية', 'يوحّد لون البشرة', 'ترطيب مكثّف يدوم طويلًا', 'يخفّف التصبّغ وآثار ما بعد الحبوب'], 'fr' => ['Réduit les taches pigmentaires', 'Unifie la couleur de la peau', 'Hydratation intense et durable', 'Atténue l’hyperpigmentation et les taches post-inflammatoires']],
                'how_to_use' => ['en' => ['Apply in the morning', 'Massage into the face in circular motions until absorbed'], 'ar' => ['يُطبّق صباحًا', 'دلّكيه على الوجه بحركات دائرية حتى الامتصاص'], 'fr' => ['Appliquer le matin', 'Masser le visage par mouvements circulaires pour faciliter la pénétration']],
                'variants' => [['name' => '50 ml', 'sku' => 'DV-CJ-50']],
                'images' => ['day-cream.jpg', 'day-cream-alt.jpg'],
            ],
            [
                'category' => 'facecare', 'price' => 30.00,
                'featured' => true, 'badges' => [ProductBadge::Bestseller->value],
                'tags' => ['brightening', 'aging', 'derm-tested'],
                'name' => ['en' => 'Clarifying Night Cream', 'ar' => 'كريم التفتيح الليلي', 'fr' => 'Crème Clarifiante Nuit'],
                'short' => ['en' => 'Overnight brightening and barrier repair.', 'ar' => 'تفتيح ليلي وإصلاح لحاجز البشرة.', 'fr' => 'Éclat et réparation de la barrière, la nuit.'],
                'description' => ['en' => 'A night clarifying cream with ceramides, niacinamide, alpha arbutin, kojic acid, vitamin C and licorice extract that brightens, evens tone and strengthens the skin barrier while you sleep.', 'ar' => 'كريم ليلي مفتّح يحتوي على السيراميد والنياسيناميد وألفا أربوتين وحمض الكوجيك وفيتامين C وخلاصة العرقسوس، يفتّح ويوحّد اللون ويقوّي حاجز البشرة أثناء النوم.', 'fr' => 'Une crème de nuit clarifiante aux céramides, niacinamide, alpha arbutine, acide kojique, vitamine C et extrait de réglisse qui illumine, unifie le teint et renforce la barrière cutanée pendant le sommeil.'],
                'ingredients' => ['en' => 'Ceramides, Niacinamide (B3), Alpha Arbutin, Kojic Acid, Vitamin C, Licorice Extract.', 'ar' => 'سيراميد، نياسيناميد (B3)، ألفا أربوتين، حمض الكوجيك، فيتامين C، خلاصة العرقسوس.', 'fr' => 'Céramides, Niacinamide (B3), Alpha arbutine, Acide kojique, Vitamine C, Extrait de réglisse.'],
                'benefits' => ['en' => ['Reduces pigmentation spots', 'Evens skin tone', 'Reinforces the skin barrier', 'Fades hyperpigmentation and post-blemish marks'], 'ar' => ['يقلّل البقع التصبغية', 'يوحّد لون البشرة', 'يقوّي حاجز البشرة', 'يخفّف التصبّغ وآثار ما بعد الحبوب'], 'fr' => ['Réduit les taches pigmentaires', 'Unifie la couleur de la peau', 'Renforce la barrière cutanée', 'Atténue l’hyperpigmentation et les taches post-inflammatoires']],
                'how_to_use' => ['en' => ['Apply at night', 'Massage into the face in circular motions until absorbed'], 'ar' => ['يُطبّق ليلًا', 'دلّكيه على الوجه بحركات دائرية حتى الامتصاص'], 'fr' => ['Appliquer la nuit', 'Masser le visage par mouvements circulaires pour faciliter la pénétration']],
                'variants' => [['name' => '50 ml', 'sku' => 'DV-CN-50']],
                'images' => ['night-cream.jpg', 'product-1.jpg'],
            ],
            [
                'category' => 'suncare', 'price' => 22.00,
                'featured' => true, 'badges' => [ProductBadge::Bestseller->value, ProductBadge::NewArrival->value],
                'tags' => ['sensitive', 'fragrance-free', 'derm-tested'],
                'name' => ['en' => 'Dermo Protect Sunscreen', 'ar' => 'ديرمو بروتكت واقٍ شمسي', 'fr' => 'Dermo Protect Crème Solaire'],
                'short' => ['en' => 'High mineral protection, no chemical filters.', 'ar' => 'حماية معدنية عالية بدون فلاتر كيميائية.', 'fr' => 'Haute protection minérale, sans filtres chimiques.'],
                'description' => ['en' => 'A broad-spectrum mineral sunscreen free of chemical filters. It protects against UVA and UVB rays with a non-greasy, fast-absorbing, non-comedogenic and fragrance-free formula suitable for every skin type.', 'ar' => 'واقٍ شمسي معدني واسع الطيف خالٍ من الفلاتر الكيميائية. يحمي من أشعة UVA وUVB بتركيبة غير دهنية سريعة الامتصاص وغير مسدّة للمسام وخالية من العطور تناسب جميع أنواع البشرة.', 'fr' => 'Un écran solaire minéral large spectre sans filtres chimiques. Il protège des rayons UVA et UVB avec une formule non grasse, à absorption rapide, non comédogène et sans parfum, adaptée à tous les types de peau.'],
                'ingredients' => ['en' => 'Zinc Oxide, natural-origin actives, Glycerin, Tocopherol (Vitamin E).', 'ar' => 'أكسيد الزنك، مكوّنات ذات أصل طبيعي، جليسرين، توكوفيرول (فيتامين E).', 'fr' => 'Oxyde de zinc, actifs d’origine naturelle, Glycérine, Tocophérol (Vitamine E).'],
                'benefits' => ['en' => ['Broad-spectrum UVA/UVB protection', 'Lightweight, non-greasy texture', 'Hypoallergenic and fragrance-free', 'Non-comedogenic — suits acne-prone skin'], 'ar' => ['حماية واسعة الطيف من UVA وUVB', 'قوام خفيف غير دهني', 'مضاد للحساسية وخالٍ من العطور', 'غير مسدّ للمسام ويناسب البشرة المعرّضة للحبوب'], 'fr' => ['Protection large spectre UVA/UVB', 'Texture légère et non grasse', 'Hypoallergénique et sans parfum', 'Non comédogène, idéal pour les peaux à imperfections']],
                'how_to_use' => ['en' => ['Apply generously before sun exposure', 'Reapply every 2 to 4 hours'], 'ar' => ['ضعيه بسخاء قبل التعرّض للشمس', 'أعيدي تطبيقه كل ساعتين إلى أربع ساعات'], 'fr' => ['Appliquer généreusement avant l’exposition au soleil', 'Renouveler l’application toutes les 2 à 4 heures']],
                'variants' => [['name' => '50 ml', 'sku' => 'DP-SS-50']],
                'images' => ['brightening-spf.jpg', 'hero-campaign.jpg'],
            ],
            [
                'category' => 'bodycare', 'price' => 20.00,
                'tags' => ['dry', 'hydration', 'brightening'],
                'name' => ['en' => 'Nourishing Body Lotion', 'ar' => 'لوشن الجسم المغذّي', 'fr' => 'Lait de Corps Nourrissant'],
                'short' => ['en' => 'Brightening, fast-absorbing daily body care.', 'ar' => 'عناية يومية مفتّحة سريعة الامتصاص للجسم.', 'fr' => 'Soin corporel éclat, à absorption rapide.'],
                'description' => ['en' => 'A nourishing body lotion with niacinamide, glycerin, alpha arbutin, kojic acid, vitamin C and glycolic acid that hydrates, gently exfoliates and evens the skin tone.', 'ar' => 'لوشن مغذٍّ للجسم يحتوي على النياسيناميد والجليسرين وألفا أربوتين وحمض الكوجيك وفيتامين C وحمض الجلايكوليك، يرطّب ويقشّر بلطف ويوحّد لون البشرة.', 'fr' => 'Un lait corporel nourrissant au niacinamide, glycérine, alpha arbutine, acide kojique, vitamine C et acide glycolique qui hydrate, exfolie en douceur et unifie le teint.'],
                'ingredients' => ['en' => 'Niacinamide (B3), Glycerin, Alpha Arbutin, Kojic Acid, Vitamin C, Glycolic Acid (AHA).', 'ar' => 'نياسيناميد (B3)، جليسرين، ألفا أربوتين، حمض الكوجيك، فيتامين C، حمض الجلايكوليك (AHA).', 'fr' => 'Niacinamide (B3), Glycérine, Alpha arbutine, Acide kojique, Vitamine C, Acide glycolique (AHA).'],
                'benefits' => ['en' => ['Deep, lasting hydration', 'Brightens and evens the skin tone', 'Gently exfoliates for smoother skin', 'Softens and comforts the body'], 'ar' => ['ترطيب عميق يدوم طويلًا', 'يفتّح ويوحّد لون البشرة', 'يقشّر بلطف لبشرة أنعم', 'ينعّم الجسم ويمنحه الراحة'], 'fr' => ['Hydratation profonde et durable', 'Éclaircit et unifie le teint', 'Exfolie en douceur pour une peau plus lisse', 'Adoucit et apaise le corps']],
                'how_to_use' => ['en' => ['Warm a generous amount between your palms for 3 seconds', 'Massage upward from ankles to waist, then wrists to shoulders, and lower abdomen upward for 2 minutes'], 'ar' => ['دفّئي كمية وفيرة بين راحتيك لمدة 3 ثوانٍ', 'دلّكيه صعودًا من الكاحلين إلى الخصر، ثم من المعصمين إلى الكتفين، ومن أسفل البطن إلى الأعلى لمدة دقيقتين'], 'fr' => ['Chauffez une noix de produit entre les paumes 3 secondes', 'Massez en remontant des chevilles vers la taille, des poignets vers les épaules, puis du bas-ventre vers le haut pendant 2 minutes']],
                'variants' => [['name' => '250 ml', 'sku' => 'DV-BL-250']],
                'images' => ['body-lotion.jpg', 'body-lotion-pump.jpg'],
            ],
            [
                'category' => 'bodycare', 'price' => 18.00,
                'badges' => [ProductBadge::NewArrival->value],
                'tags' => ['sensitive', 'fragrance-free', 'derm-tested'],
                'name' => ['en' => 'Dermo Cica Repair Cream', 'ar' => 'ديرمو سيكا كريم مصلح', 'fr' => 'Dermo Cica Crème Cicatrisante'],
                'short' => ['en' => 'Soothing repair cream for fast healing.', 'ar' => 'كريم مهدّئ ومصلح لالتئام سريع.', 'fr' => 'Crème réparatrice apaisante pour une guérison rapide.'],
                'description' => ['en' => 'A repairing cream with beeswax, beta-sitosterol, panthenol, aloe vera, zinc oxide and plant oils that forms a breathable barrier, soothes and speeds up skin recovery. Fragrance-free and suitable for all ages, including during pregnancy and breastfeeding.', 'ar' => 'كريم مصلح يحتوي على شمع العسل وبيتا-سيتوستيرول والبانثينول والصبار وأكسيد الزنك وزيوت نباتية، يشكّل طبقة حاجزة تسمح بالتنفّس ويهدّئ ويسرّع تعافي البشرة. خالٍ من العطور ويناسب جميع الأعمار، بما في ذلك أثناء الحمل والرضاعة.', 'fr' => 'Une crème réparatrice à la cire d’abeille, au bêta-sitostérol, au panthénol, à l’aloe vera, à l’oxyde de zinc et aux huiles végétales qui forme une barrière respirante, apaise et accélère la réparation de la peau. Sans parfum et convient à tous les âges, y compris pendant la grossesse et l’allaitement.'],
                'ingredients' => ['en' => 'Beeswax, Beta-sitosterol, Panthenol, Aloe Vera Extract, Zinc Oxide, Sesame Oil, Olive Oil, Almond Oil.', 'ar' => 'شمع العسل، بيتا-سيتوستيرول، بانثينول، خلاصة الصبار، أكسيد الزنك، زيت السمسم، زيت الزيتون، زيت اللوز.', 'fr' => 'Cire d’abeille, Bêta-sitostérol, Panthénol, Extrait d’aloe vera, Oxyde de zinc, Huile de sésame, Huile d’olive, Huile d’amande.'],
                'benefits' => ['en' => ['Forms a breathable protective barrier', 'Reduces inflammation and itching', 'Deeply hydrates and speeds regeneration', 'Supports skin repair'], 'ar' => ['يشكّل طبقة حاجزة واقية تسمح بالتنفّس', 'يقلّل الالتهاب والحكة', 'يرطّب بعمق ويسرّع التجدّد', 'يدعم إصلاح البشرة'], 'fr' => ['Forme une barrière respirante protectrice', 'Réduit les inflammations et démangeaisons', 'Hydrate en profondeur et accélère la régénération', 'Soutient la réparation de la peau']],
                'how_to_use' => ['en' => ['Apply a thin layer to wounds, cuts or burns 2–3 times a day', 'Cover with a bandage or dressing if needed'], 'ar' => ['ضعي طبقة رقيقة على الجروح أو الحروق مرتين إلى ثلاث مرات يوميًا', 'غطّيها بضمادة أو لاصق عند الحاجة'], 'fr' => ['Appliquer une fine couche sur les plaies, blessures ou brûlures 2 à 3 fois par jour', 'Recouvrir d’un pansement si nécessaire']],
                'variants' => [['name' => '50 ml', 'sku' => 'DC-RC-50']],
                'images' => ['product-1.jpg', 'collection.jpg'],
            ],
            [
                'category' => 'facecare', 'price' => 24.00,
                'tags' => ['sensitive', 'hydration', 'fragrance-free', 'derm-tested'],
                'name' => ['en' => 'Dermanthenol Repair Cream', 'ar' => 'ديرمانثينول كريم مرمّم', 'fr' => 'Dermanthenol Crème Réparatrice'],
                'short' => ['en' => 'Panthenol, ceramide & hyaluronic repair.', 'ar' => 'إصلاح بالبانثينول والسيراميد وحمض الهيالورونيك.', 'fr' => 'Réparation panthénol, céramide & acide hyaluronique.'],
                'description' => ['en' => 'A restorative cream with panthenol (provitamin B5), ceramides and hyaluronic acid that deeply soothes, repairs and reinforces the skin barrier for soft, revitalised and visibly healthier skin.', 'ar' => 'كريم مرمّم يحتوي على البانثينول (بروفيتامين B5) والسيراميد وحمض الهيالورونيك، يهدّئ ويصلح ويقوّي حاجز البشرة بعمق لبشرة ناعمة ومتجدّدة وأكثر صحة.', 'fr' => 'Une crème réparatrice au panthénol (provitamine B5), aux céramides et à l’acide hyaluronique qui apaise, répare et renforce en profondeur la barrière cutanée pour une peau douce, revitalisée et visiblement plus saine.'],
                'ingredients' => ['en' => 'Panthenol (Provitamin B5), Ceramides, Hyaluronic Acid.', 'ar' => 'بانثينول (بروفيتامين B5)، سيراميد، حمض الهيالورونيك.', 'fr' => 'Panthénol (Provitamine B5), Céramides, Acide hyaluronique.'],
                'benefits' => ['en' => ['Intense, long-lasting hydration', 'Repairs and soothes irritated skin', 'Reinforces the skin barrier', 'Regeneration and natural radiance'], 'ar' => ['ترطيب مكثّف يدوم طويلًا', 'يصلح ويهدّئ البشرة المتهيّجة', 'يقوّي حاجز البشرة', 'تجدّد وإشراقة طبيعية'], 'fr' => ['Hydratation intense et longue durée', 'Répare et apaise les peaux irritées', 'Renforce la barrière cutanée', 'Régénération et éclat naturel']],
                'how_to_use' => ['en' => ['Apply to dry or irritated areas', 'Use morning and/or night until absorbed'], 'ar' => ['ضعيه على المناطق الجافة أو المتهيّجة', 'استخدميه صباحًا و/أو مساءً حتى الامتصاص'], 'fr' => ['Appliquer sur les zones sèches ou irritées', 'Utiliser matin et/ou soir jusqu’à absorption']],
                'variants' => [['name' => '50 ml', 'sku' => 'DM-RC-50']],
                'images' => ['collection.jpg', 'serum.jpg'],
            ],
        ];
    }

    protected function seedHeroSlides(): void
    {
        $slides = [
            [
                'title' => ['en' => 'Radiance, rooted in science', 'ar' => 'إشراقةٌ نابعةٌ من العلم', 'fr' => 'L’éclat, enraciné dans la science'],
                'subtitle' => ['en' => 'Discover the Dermovive skincare edit.', 'ar' => 'اكتشفي تشكيلة ديرموفيف للعناية بالبشرة.', 'fr' => 'Découvrez la sélection soin Dermovive.'],
                'cta_label' => ['en' => 'Shop skincare', 'ar' => 'تسوّقي العناية', 'fr' => 'Voir les soins'],
                'link_type' => LinkType::Category, 'link_target' => 'cosmetics-skincare',
                'image' => 'hero-campaign.jpg',
            ],
            [
                'title' => ['en' => 'Clarify & even your tone', 'ar' => 'صفاءٌ وتوحيدٌ للون بشرتك', 'fr' => 'Clarifiez et unifiez votre teint'],
                'subtitle' => ['en' => 'The Clarifying Day Cream for a radiant complexion.', 'ar' => 'كريم التفتيح النهاري لإطلالة مشرقة.', 'fr' => 'La Crème Clarifiante Jour pour un teint éclatant.'],
                'cta_label' => ['en' => 'Discover', 'ar' => 'اكتشفي', 'fr' => 'Découvrir'],
                'link_type' => LinkType::Product, 'link_target' => 'clarifying-day-cream',
                'image' => 'hero-shelf.jpg',
            ],
            [
                'title' => ['en' => 'Sun care, reimagined', 'ar' => 'حماية شمسية بحلّة جديدة', 'fr' => 'La protection solaire réinventée'],
                'subtitle' => ['en' => 'Mineral protection, no chemical filters.', 'ar' => 'حماية معدنية بدون فلاتر كيميائية.', 'fr' => 'Protection minérale, sans filtres chimiques.'],
                'cta_label' => ['en' => 'Protect now', 'ar' => 'احمي بشرتك', 'fr' => 'Se protéger'],
                'link_type' => LinkType::Category, 'link_target' => 'sun-care',
                'image' => 'collection.jpg',
            ],
        ];

        foreach ($slides as $i => $slide) {
            $heroSlide = HeroSlide::create([
                ...collect($slide)->except('image')->all(),
                'sort_order' => $i,
                'is_active' => true,
            ]);

            $this->attachMedia($heroSlide, 'image', $slide['image'], $heroSlide->getTranslation('title', 'en'));
        }
    }

    protected function seedPages(): void
    {
        Page::create([
            'title' => ['en' => 'Our Story', 'ar' => 'قصتنا', 'fr' => 'Notre histoire'],
            'body' => [
                'en' => '<figure><img src="/brand/hero-dermo-shelf.jpg" alt="Dermovive products arranged in a skincare shelf" /></figure><p>Dermovive Pharma is a cosmetics laboratory specializing in the development and manufacturing of skincare products. We are committed to delivering innovative skincare solutions that combine efficacy, safety, and premium-quality ingredients, tailored to the needs of African and international markets.</p><h2>Our Vision</h2><p>To empower self-confidence through safe and effective cosmetic products formulated with natural ingredients that enhance overall skin health and reveal natural beauty. We aspire to expand our presence across all West African countries by 2030, establishing Dermovive Pharma as a trusted leader in the cosmetics industry.</p><h2>Our Mission</h2><p>To become the preferred choice for women worldwide by offering innovative, sustainable and affordable skincare solutions — providing high-quality products at accessible prices for all social and economic groups, while celebrating diversity and promoting natural beauty.</p>',
                'ar' => '<figure><img src="/brand/hero-dermo-shelf.jpg" alt="منتجات ديرموفيف مرتبة على رف للعناية بالبشرة" /></figure><p>ديرموفيف فارما مختبر لمستحضرات التجميل متخصص في تطوير وتصنيع منتجات العناية بالبشرة. نلتزم بتقديم حلول مبتكرة للعناية بالبشرة تجمع بين الفعالية والأمان والمكوّنات عالية الجودة، مصمّمة لتلبية احتياجات الأسواق الأفريقية والعالمية.</p><h2>رؤيتنا</h2><p>تعزيز الثقة بالنفس من خلال منتجات تجميل آمنة وفعّالة مصنوعة من مكوّنات طبيعية تعزّز صحة البشرة وتُبرز الجمال الطبيعي. نطمح إلى التوسّع في جميع دول غرب أفريقيا بحلول عام 2030 لنصبح روّادًا موثوقين في صناعة التجميل.</p><h2>رسالتنا</h2><p>أن نصبح الخيار المفضّل للنساء حول العالم بتقديم حلول عناية مبتكرة ومستدامة وبأسعار في المتناول، مع توفير منتجات عالية الجودة بأسعار تناسب جميع الفئات الاجتماعية والاقتصادية، والاحتفاء بالتنوّع وتعزيز الجمال الطبيعي.</p>',
                'fr' => '<figure><img src="/brand/hero-dermo-shelf.jpg" alt="Produits Dermovive disposés sur une étagère de soin" /></figure><p>Dermovive Pharma est un laboratoire cosmétique spécialisé dans le développement et la fabrication de produits de soin. Nous nous engageons à proposer des solutions de soin innovantes alliant efficacité, sécurité et ingrédients de qualité premium, adaptées aux besoins des marchés africains et internationaux.</p><h2>Notre Vision</h2><p>Renforcer la confiance en soi grâce à des produits cosmétiques sûrs et efficaces, formulés avec des ingrédients naturels qui améliorent la santé de la peau et révèlent la beauté naturelle. Nous aspirons à étendre notre présence dans toute l’Afrique de l’Ouest d’ici 2030, faisant de Dermovive Pharma un leader de confiance dans l’industrie cosmétique.</p><h2>Notre Mission</h2><p>Devenir le choix préféré des femmes du monde entier en offrant des soins innovants, durables et abordables — des produits de haute qualité à des prix accessibles à tous les groupes sociaux et économiques, tout en célébrant la diversité et en valorisant la beauté naturelle.</p>',
            ],
            'is_published' => true,
        ]);

        Page::create([
            'title' => ['en' => 'Contact', 'ar' => 'تواصلي معنا', 'fr' => 'Contact'],
            'body' => [
                'en' => '<figure><img src="/brand/collection-dermo-white.jpg" alt="Dermovive skincare product collection" /></figure><p>Questions about our products? Our team is here to help you choose the right Dermovive routine for your skin.</p><ul><li><strong>Email:</strong> dermovivepharmasn@gmail.com</li><li><strong>Phone:</strong> +221 77 486 2247</li><li><strong>Address:</strong> Dakar, Sénégal</li></ul>',
                'ar' => '<figure><img src="/brand/collection-dermo-white.jpg" alt="مجموعة منتجات ديرموفيف للعناية بالبشرة" /></figure><p>هل لديك أسئلة حول منتجاتنا؟ فريقنا هنا لمساعدتك على اختيار روتين ديرموفيف المناسب لبشرتك.</p><ul><li><strong>البريد الإلكتروني:</strong> dermovivepharmasn@gmail.com</li><li><strong>الهاتف:</strong> +221 77 486 2247</li><li><strong>العنوان:</strong> داكار، السنغال</li></ul>',
                'fr' => '<figure><img src="/brand/collection-dermo-white.jpg" alt="Collection de produits de soin Dermovive" /></figure><p>Des questions sur nos produits ? Notre équipe vous aide à choisir le rituel Dermovive adapté à votre peau.</p><ul><li><strong>E-mail :</strong> dermovivepharmasn@gmail.com</li><li><strong>Téléphone :</strong> +221 77 486 2247</li><li><strong>Adresse :</strong> Dakar, Sénégal</li></ul>',
            ],
            'is_published' => true,
        ]);
    }

    protected function seedSettings(): void
    {
        Setting::set('site_name', 'Dermovive Pharma');
        Setting::set('tagline', 'Innovative, safe and effective skincare — crafted for African and international skin.');
        Setting::set('contact_email', 'dermovivepharmasn@gmail.com');
        Setting::set('contact_phone', '+221 77 486 2247');
        Setting::set('address', 'Dakar, Sénégal');
    }

    protected function seedCategoryMedia(): void
    {
        $images = [
            'cosmetics' => 'collection.jpg',
            'supplements' => 'product-1.jpg',
            'devices' => 'hero-shelf.jpg',
            'cleansers' => 'cleanser.jpg',
            'facecare' => 'day-cream.jpg',
            'suncare' => 'brightening-spf.jpg',
            'bodycare' => 'body-lotion-pump.jpg',
        ];

        foreach ($images as $key => $image) {
            if (isset($this->cat[$key])) {
                $this->attachMedia($this->cat[$key], 'image', $image, $this->cat[$key]->getTranslation('name', 'en'));
            }
        }
    }

    protected function attachMedia(Model&HasMedia $model, string $collection, string $filename, ?string $name = null): void
    {
        $path = $this->assetPath($filename);

        if (! $path) {
            $this->command?->warn("Seed media missing: {$filename}");

            return;
        }

        $model
            ->addMedia($path)
            ->preservingOriginal()
            ->usingName($name ?? pathinfo($filename, PATHINFO_FILENAME))
            ->toMediaCollection($collection);
    }

    protected function assetPath(string $filename): ?string
    {
        $path = database_path('seeders/media/'.$filename);

        return is_file($path) ? $path : null;
    }
}
