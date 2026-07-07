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
        // Level 1
        $this->makeCategory('skincare', ['en' => 'Skincare', 'ar' => 'العناية بالبشرة', 'fr' => 'Soins de la peau']);
        $this->makeCategory('makeup', ['en' => 'Makeup', 'ar' => 'المكياج', 'fr' => 'Maquillage']);
        $this->makeCategory('body', ['en' => 'Body', 'ar' => 'العناية بالجسم', 'fr' => 'Corps']);

        // Level 2 (under Skincare)
        $this->makeCategory('cleansers', ['en' => 'Cleansers', 'ar' => 'المنظفات', 'fr' => 'Nettoyants'], 'skincare');
        $this->makeCategory('serums', ['en' => 'Serums', 'ar' => 'السيرومات', 'fr' => 'Sérums'], 'skincare');
        $this->makeCategory('moisturisers', ['en' => 'Moisturisers', 'ar' => 'المرطبات', 'fr' => 'Hydratants'], 'skincare');
        $this->makeCategory('suncare', ['en' => 'Sun Care', 'ar' => 'الحماية من الشمس', 'fr' => 'Protection solaire'], 'skincare');

        // Level 3 (under Cleansers)
        $this->makeCategory('gentle-cleansers', ['en' => 'Gentle Cleansers', 'ar' => 'منظفات لطيفة', 'fr' => 'Nettoyants doux'], 'cleansers');
        $this->makeCategory('exfoliators', ['en' => 'Exfoliators', 'ar' => 'المقشرات', 'fr' => 'Exfoliants'], 'cleansers');

        // Level 2 (under Makeup)
        $this->makeCategory('face', ['en' => 'Face', 'ar' => 'الوجه', 'fr' => 'Visage'], 'makeup');
        $this->makeCategory('lips', ['en' => 'Lips', 'ar' => 'الشفاه', 'fr' => 'Lèvres'], 'makeup');

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
                'category' => 'serums', 'price' => 32.00, 'compare_at' => 40.00,
                'featured' => true, 'badges' => [ProductBadge::Bestseller->value],
                'tags' => ['dry', 'hydration', 'vegan', 'derm-tested'],
                'name' => ['en' => 'Hydra-Glow Serum', 'ar' => 'سيروم هيدرا غلو', 'fr' => 'Sérum Hydra-Glow'],
                'short' => ['en' => 'Plumping hydration with niacinamide + B5.', 'ar' => 'ترطيب ممتلئ مع النياسيناميد وفيتامين B5.', 'fr' => 'Hydratation repulpante au niacinamide + B5.'],
                'description' => ['en' => 'A lightweight serum that floods skin with moisture and a healthy glow.', 'ar' => 'سيروم خفيف يغمر البشرة بالترطيب وإشراقة صحية.', 'fr' => 'Un sérum léger qui inonde la peau d’hydratation et d’éclat.'],
                'ingredients' => ['en' => 'Aqua, Niacinamide, Panthenol, Sodium Hyaluronate, Glycerin.', 'ar' => 'ماء، نياسيناميد، بانثينول، هيالورونات الصوديوم، جليسرين.', 'fr' => 'Aqua, Niacinamide, Panthénol, Hyaluronate de sodium, Glycérine.'],
                'benefits' => ['en' => ['Deep hydration', 'Visibly plumper skin', 'Healthy glow'], 'ar' => ['ترطيب عميق', 'بشرة أكثر امتلاءً', 'إشراقة صحية'], 'fr' => ['Hydratation profonde', 'Peau visiblement repulpée', 'Éclat sain']],
                'how_to_use' => ['en' => ['Apply 3–4 drops to clean skin', 'Pat gently until absorbed', 'Follow with moisturiser'], 'ar' => ['ضعي 3–4 قطرات على بشرة نظيفة', 'دلّكي بلطف حتى الامتصاص', 'اتبعيها بمرطب'], 'fr' => ['Appliquez 3–4 gouttes sur peau propre', 'Tapotez jusqu’à absorption', 'Faites suivre d’un hydratant']],
                'variants' => [['name' => '30 ml', 'sku' => 'HG-30', 'price' => 32.00], ['name' => '50 ml', 'sku' => 'HG-50', 'price' => 46.00]],
                'images' => ['serum.jpg', 'collection.jpg'],
            ],
            [
                'category' => 'serums', 'price' => 38.00,
                'featured' => true, 'badges' => [ProductBadge::Bestseller->value],
                'tags' => ['combination', 'brightening', 'vegan'],
                'name' => ['en' => 'Vitamin C Brightening Serum', 'ar' => 'سيروم فيتامين سي المفتّح', 'fr' => 'Sérum Éclat Vitamine C'],
                'short' => ['en' => '15% vitamin C for radiant, even tone.', 'ar' => '15% فيتامين سي لبشرة مشرقة وموحدة.', 'fr' => '15% de vitamine C pour un teint éclatant.'],
                'description' => ['en' => 'A potent antioxidant serum that brightens and evens skin tone over time.', 'ar' => 'سيروم غني بمضادات الأكسدة يفتّح ويوحّد لون البشرة مع الوقت.', 'fr' => 'Un sérum antioxydant qui illumine et unifie le teint.'],
                'ingredients' => ['en' => 'Aqua, Ascorbic Acid, Ferulic Acid, Vitamin E, Glycerin.', 'ar' => 'ماء، حمض الأسكوربيك، حمض الفيروليك، فيتامين هـ، جليسرين.', 'fr' => 'Aqua, Acide ascorbique, Acide férulique, Vitamine E, Glycérine.'],
                'benefits' => ['en' => ['Brightens dark spots', 'Evens skin tone', 'Antioxidant protection'], 'ar' => ['يفتّح البقع الداكنة', 'يوحّد لون البشرة', 'حماية مضادة للأكسدة'], 'fr' => ['Atténue les taches', 'Unifie le teint', 'Protection antioxydante']],
                'how_to_use' => ['en' => ['Use in the morning', 'Apply before moisturiser', 'Always follow with SPF'], 'ar' => ['استخدميه صباحًا', 'ضعيه قبل المرطب', 'اتبعيه دائمًا بواقٍ شمسي'], 'fr' => ['À utiliser le matin', 'Appliquer avant l’hydratant', 'Toujours faire suivre d’un SPF']],
                'variants' => [['name' => '30 ml', 'sku' => 'VC-30', 'price' => 38.00]],
                'images' => ['brightening-spf.jpg', 'hero-campaign.jpg'],
            ],
            [
                'category' => 'gentle-cleansers', 'price' => 18.00,
                'badges' => [ProductBadge::NewArrival->value],
                'tags' => ['sensitive', 'fragrance-free', 'derm-tested'],
                'name' => ['en' => 'Gentle Foaming Cleanser', 'ar' => 'غسول رغوي لطيف', 'fr' => 'Nettoyant Moussant Doux'],
                'short' => ['en' => 'Soft foam that cleanses without stripping.', 'ar' => 'رغوة ناعمة تنظّف دون أن تجفّف.', 'fr' => 'Mousse douce qui nettoie sans dessécher.'],
                'description' => ['en' => 'A pH-balanced cleanser for sensitive skin, leaving it soft and comfortable.', 'ar' => 'غسول متوازن الحموضة للبشرة الحساسة يتركها ناعمة ومريحة.', 'fr' => 'Un nettoyant au pH équilibré pour peaux sensibles.'],
                'ingredients' => ['en' => 'Aqua, Coco-Glucoside, Glycerin, Panthenol, Allantoin.', 'ar' => 'ماء، كوكو-جلوكوسيد، جليسرين، بانثينول، ألانتوين.', 'fr' => 'Aqua, Coco-Glucoside, Glycérine, Panthénol, Allantoïne.'],
                'benefits' => ['en' => ['Gentle on sensitive skin', 'No tight feeling', 'Daily use'], 'ar' => ['لطيف على البشرة الحساسة', 'دون شعور بالشدّ', 'للاستخدام اليومي'], 'fr' => ['Doux pour peaux sensibles', 'Sans tiraillement', 'Usage quotidien']],
                'how_to_use' => ['en' => ['Massage onto damp skin', 'Rinse with lukewarm water', 'Use morning and night'], 'ar' => ['دلّكيه على بشرة مبللة', 'اشطفيه بماء فاتر', 'استخدميه صباحًا ومساءً'], 'fr' => ['Massez sur peau humide', 'Rincez à l’eau tiède', 'Matin et soir']],
                'variants' => [['name' => '150 ml', 'sku' => 'GC-150']],
                'images' => ['cleanser.jpg', 'collection.jpg'],
            ],
            [
                'category' => 'moisturisers', 'price' => 26.00,
                'tags' => ['dry', 'hydration', 'derm-tested'],
                'name' => ['en' => 'Daily Moisture Cream', 'ar' => 'كريم الترطيب اليومي', 'fr' => 'Crème Hydratante Quotidienne'],
                'short' => ['en' => '24h moisture with ceramides.', 'ar' => 'ترطيب 24 ساعة مع السيراميد.', 'fr' => 'Hydratation 24h aux céramides.'],
                'description' => ['en' => 'A rich-yet-fast-absorbing cream that strengthens the skin barrier.', 'ar' => 'كريم غني سريع الامتصاص يقوّي حاجز البشرة.', 'fr' => 'Une crème riche à absorption rapide qui renforce la barrière cutanée.'],
                'ingredients' => ['en' => 'Aqua, Ceramide NP, Shea Butter, Squalane, Glycerin.', 'ar' => 'ماء، سيراميد NP، زبدة الشيا، سكوالان، جليسرين.', 'fr' => 'Aqua, Céramide NP, Beurre de karité, Squalane, Glycérine.'],
                'benefits' => ['en' => ['24-hour hydration', 'Strengthens barrier', 'Non-greasy'], 'ar' => ['ترطيب 24 ساعة', 'يقوّي الحاجز', 'غير دهني'], 'fr' => ['Hydratation 24h', 'Renforce la barrière', 'Non gras']],
                'how_to_use' => ['en' => ['Apply to face and neck', 'Use after serum', 'Morning and night'], 'ar' => ['ضعيه على الوجه والرقبة', 'استخدميه بعد السيروم', 'صباحًا ومساءً'], 'fr' => ['Appliquez sur visage et cou', 'Après le sérum', 'Matin et soir']],
                'variants' => [['name' => '50 ml', 'sku' => 'DM-50']],
                'images' => ['day-cream.jpg', 'day-cream-alt.jpg'],
            ],
            [
                'category' => 'suncare', 'price' => 24.00,
                'featured' => true, 'badges' => [ProductBadge::Bestseller->value, ProductBadge::NewArrival->value],
                'tags' => ['sensitive', 'fragrance-free', 'derm-tested'],
                'name' => ['en' => 'Mineral Sunscreen SPF 50', 'ar' => 'واقٍ شمسي معدني SPF 50', 'fr' => 'Écran Minéral SPF 50'],
                'short' => ['en' => 'Invisible mineral protection, no white cast.', 'ar' => 'حماية معدنية شفافة دون أثر أبيض.', 'fr' => 'Protection minérale invisible, sans traces blanches.'],
                'description' => ['en' => 'Broad-spectrum SPF 50 with a weightless, skin-tone-adapting finish.', 'ar' => 'حماية واسعة الطيف SPF 50 بلمسة خفيفة تتكيف مع لون البشرة.', 'fr' => 'SPF 50 large spectre à la finition légère adaptée au teint.'],
                'ingredients' => ['en' => 'Zinc Oxide, Aqua, Glycerin, Niacinamide, Tocopherol.', 'ar' => 'أكسيد الزنك، ماء، جليسرين، نياسيناميد، توكوفيرول.', 'fr' => 'Oxyde de zinc, Aqua, Glycérine, Niacinamide, Tocophérol.'],
                'benefits' => ['en' => ['SPF 50 broad spectrum', 'No white cast', 'Reef-friendly'], 'ar' => ['حماية SPF 50 واسعة الطيف', 'دون أثر أبيض', 'آمن للشعاب المرجانية'], 'fr' => ['SPF 50 large spectre', 'Sans traces blanches', 'Respecte les récifs']],
                'how_to_use' => ['en' => ['Apply as the last step each morning', 'Reapply every 2 hours outdoors'], 'ar' => ['ضعيه كخطوة أخيرة كل صباح', 'أعيدي تطبيقه كل ساعتين في الخارج'], 'fr' => ['Dernière étape chaque matin', 'Réappliquez toutes les 2 h à l’extérieur']],
                'variants' => [['name' => '50 ml', 'sku' => 'SS-50']],
                'images' => ['brightening-spf.jpg', 'hero-campaign.jpg'],
            ],
            [
                'category' => 'exfoliators', 'price' => 22.00,
                'tags' => ['oily', 'acne', 'vegan'],
                'name' => ['en' => 'AHA Exfoliating Tonic', 'ar' => 'تونر التقشير بحمض AHA', 'fr' => 'Tonique Exfoliant AHA'],
                'short' => ['en' => 'Smooths texture and refines pores.', 'ar' => 'ينعّم الملمس ويصقل المسام.', 'fr' => 'Lisse le grain et affine les pores.'],
                'description' => ['en' => 'A gentle glycolic tonic that resurfaces for smoother, brighter skin.', 'ar' => 'تونر جلايكوليك لطيف يجدّد البشرة لملمس أنعم وأكثر إشراقًا.', 'fr' => 'Un tonique glycolique doux pour une peau plus lisse et lumineuse.'],
                'ingredients' => ['en' => 'Aqua, Glycolic Acid, Aloe Vera, Panthenol.', 'ar' => 'ماء، حمض الجلايكوليك، صبار، بانثينول.', 'fr' => 'Aqua, Acide glycolique, Aloe vera, Panthénol.'],
                'benefits' => ['en' => ['Smooths texture', 'Refines pores', 'Boosts radiance'], 'ar' => ['ينعّم الملمس', 'يصقل المسام', 'يعزّز الإشراق'], 'fr' => ['Lisse le grain', 'Affine les pores', 'Ravive l’éclat']],
                'how_to_use' => ['en' => ['Sweep over clean skin at night', 'Start 2–3 times a week', 'Always use SPF next day'], 'ar' => ['مرّريه على بشرة نظيفة ليلًا', 'ابدئي 2–3 مرات أسبوعيًا', 'استخدمي الواقي الشمسي في اليوم التالي'], 'fr' => ['Passez sur peau propre le soir', 'Commencez 2–3 fois/semaine', 'SPF le lendemain']],
                'variants' => [['name' => '200 ml', 'sku' => 'AHA-200']],
                'images' => ['night-cream.jpg', 'product-1.jpg'],
            ],
            [
                'category' => 'lips', 'price' => 16.00,
                'badges' => [ProductBadge::NewArrival->value],
                'tags' => ['vegan'],
                'name' => ['en' => 'Velvet Matte Lipstick', 'ar' => 'أحمر شفاه مطفي مخملي', 'fr' => 'Rouge à Lèvres Mat Velours'],
                'short' => ['en' => 'Full-coverage matte that feels weightless.', 'ar' => 'تغطية كاملة مطفية بإحساس خفيف.', 'fr' => 'Mat couvrant à la sensation légère.'],
                'description' => ['en' => 'A creamy matte lipstick with long wear and a comfortable feel.', 'ar' => 'أحمر شفاه كريمي مطفي يدوم طويلًا بإحساس مريح.', 'fr' => 'Un rouge à lèvres mat crémeux longue tenue et confortable.'],
                'ingredients' => ['en' => 'Caprylic/Capric Triglyceride, Jojoba Oil, Vitamin E, Pigments.', 'ar' => 'ثلاثي الجليسريد، زيت الجوجوبا، فيتامين هـ، أصباغ.', 'fr' => 'Triglycéride caprylique, Huile de jojoba, Vitamine E, Pigments.'],
                'benefits' => ['en' => ['Long wear', 'Comfortable matte', 'Vegan formula'], 'ar' => ['ثبات طويل', 'مطفي مريح', 'تركيبة نباتية'], 'fr' => ['Longue tenue', 'Mat confortable', 'Formule végane']],
                'how_to_use' => ['en' => ['Line lips then fill', 'Blot for a softer finish'], 'ar' => ['حدّدي الشفاه ثم املئيها', 'اضغطي بمنديل للمسة أنعم'], 'fr' => ['Tracez puis remplissez', 'Tamponnez pour un fini plus doux']],
                'variants' => [['name' => 'Rosewood', 'sku' => 'VL-RW'], ['name' => 'Terracotta', 'sku' => 'VL-TC'], ['name' => 'Classic Red', 'sku' => 'VL-CR']],
                'images' => ['beauty-face.jpg', 'hero-shelf.jpg'],
            ],
            [
                'category' => 'body', 'price' => 20.00,
                'tags' => ['dry', 'hydration', 'fragrance-free'],
                'name' => ['en' => 'Nourishing Body Lotion', 'ar' => 'لوشن الجسم المغذّي', 'fr' => 'Lait Corporel Nourrissant'],
                'short' => ['en' => 'Silky, fast-absorbing daily body care.', 'ar' => 'عناية يومية حريرية سريعة الامتصاص.', 'fr' => 'Soin corporel soyeux à absorption rapide.'],
                'description' => ['en' => 'A nourishing lotion that softens and hydrates for 24 hours.', 'ar' => 'لوشن مغذٍّ ينعّم ويرطّب لمدة 24 ساعة.', 'fr' => 'Un lait nourrissant qui adoucit et hydrate 24 heures.'],
                'ingredients' => ['en' => 'Aqua, Shea Butter, Glycerin, Oat Extract, Squalane.', 'ar' => 'ماء، زبدة الشيا، جليسرين، خلاصة الشوفان، سكوالان.', 'fr' => 'Aqua, Beurre de karité, Glycérine, Extrait d’avoine, Squalane.'],
                'benefits' => ['en' => ['24h softness', 'Fast absorbing', 'Fragrance-free'], 'ar' => ['نعومة 24 ساعة', 'سريع الامتصاص', 'خالٍ من العطور'], 'fr' => ['Douceur 24h', 'Absorption rapide', 'Sans parfum']],
                'how_to_use' => ['en' => ['Massage over body after showering', 'Use daily'], 'ar' => ['دلّكيه على الجسم بعد الاستحمام', 'استخدميه يوميًا'], 'fr' => ['Massez sur le corps après la douche', 'Usage quotidien']],
                'variants' => [['name' => '250 ml', 'sku' => 'BL-250']],
                'images' => ['body-lotion-pump.jpg', 'body-lotion.jpg'],
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
                'link_type' => LinkType::Category, 'link_target' => 'skincare',
                'image' => 'hero-campaign.jpg',
            ],
            [
                'title' => ['en' => 'New: Vitamin C Glow', 'ar' => 'جديد: إشراقة فيتامين سي', 'fr' => 'Nouveau : Éclat Vitamine C'],
                'subtitle' => ['en' => 'Brighten and even your tone.', 'ar' => 'فتّحي ووحّدي لون بشرتك.', 'fr' => 'Illuminez et unifiez votre teint.'],
                'cta_label' => ['en' => 'Discover', 'ar' => 'اكتشفي', 'fr' => 'Découvrir'],
                'link_type' => LinkType::Product, 'link_target' => 'vitamin-c-brightening-serum',
                'image' => 'hero-shelf.jpg',
            ],
            [
                'title' => ['en' => 'Sun care, reimagined', 'ar' => 'حماية شمسية بحلّة جديدة', 'fr' => 'La protection solaire réinventée'],
                'subtitle' => ['en' => 'Invisible SPF 50 for every day.', 'ar' => 'حماية SPF 50 شفافة لكل يوم.', 'fr' => 'SPF 50 invisible au quotidien.'],
                'cta_label' => ['en' => 'Protect now', 'ar' => 'احمي بشرتك', 'fr' => 'Se protéger'],
                'link_type' => LinkType::Category, 'link_target' => 'suncare',
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
                'en' => '<figure><img src="/brand/hero-dermo-shelf.jpg" alt="Dermovive products arranged in a skincare shelf" /></figure><p>Dermovive Pharma was founded by pharmacists who believe effective skincare should also feel beautiful. Every formula blends clinical research with sensorial care.</p><h2>Formulas with a visible purpose</h2><p>From daily SPF to night creams and body care, each product is developed around real routines, gentle textures, and skin-first performance.</p>',
                'ar' => '<figure><img src="/brand/hero-dermo-shelf.jpg" alt="منتجات ديرموفيف مرتبة على رف للعناية بالبشرة" /></figure><p>تأسست ديرموفيف فارما على يد صيادلة يؤمنون بأن العناية الفعّالة بالبشرة يجب أن تكون جميلة أيضًا. كل تركيبة تمزج بين الأبحاث السريرية والعناية الحسّية.</p><h2>تركيبات بهدف واضح</h2><p>من الواقي الشمسي اليومي إلى كريمات الليل والعناية بالجسم، كل منتج مصمم حول روتين حقيقي وملمس لطيف وأداء يضع البشرة أولًا.</p>',
                'fr' => '<figure><img src="/brand/hero-dermo-shelf.jpg" alt="Produits Dermovive disposés sur une étagère de soin" /></figure><p>Dermovive Pharma a été fondée par des pharmaciens convaincus qu’un soin efficace doit aussi être beau. Chaque formule allie recherche clinique et plaisir sensoriel.</p><h2>Des formules à la mission visible</h2><p>Du SPF quotidien aux crèmes de nuit et soins du corps, chaque produit est pensé pour les vrais rituels, les textures douces et la performance cutanée.</p>',
            ],
            'is_published' => true,
        ]);

        Page::create([
            'title' => ['en' => 'Contact', 'ar' => 'تواصلي معنا', 'fr' => 'Contact'],
            'body' => [
                'en' => '<figure><img src="/brand/collection-dermo-white.jpg" alt="Dermovive skincare product collection" /></figure><p>Questions about our products? Our team is here to help you choose the right Dermovive routine for your skin.</p>',
                'ar' => '<figure><img src="/brand/collection-dermo-white.jpg" alt="مجموعة منتجات ديرموفيف للعناية بالبشرة" /></figure><p>هل لديك أسئلة حول منتجاتنا؟ فريقنا هنا لمساعدتك على اختيار روتين ديرموفيف المناسب لبشرتك.</p>',
                'fr' => '<figure><img src="/brand/collection-dermo-white.jpg" alt="Collection de produits de soin Dermovive" /></figure><p>Des questions sur nos produits ? Notre équipe vous aide à choisir le rituel Dermovive adapté à votre peau.</p>',
            ],
            'is_published' => true,
        ]);
    }

    protected function seedSettings(): void
    {
        Setting::set('site_name', 'Dermovive Pharma');
        Setting::set('tagline', 'Science-led skincare, crafted to be loved.');
        Setting::set('contact_email', 'hello@dermovive.test');
        Setting::set('contact_phone', '+962 7 0000 0000');
        Setting::set('address', 'Amman, Jordan');
    }

    protected function seedCategoryMedia(): void
    {
        $images = [
            'skincare' => 'collection.jpg',
            'makeup' => 'beauty-face.jpg',
            'body' => 'body-lotion-pump.jpg',
            'cleansers' => 'cleanser.jpg',
            'serums' => 'serum.jpg',
            'moisturisers' => 'day-cream.jpg',
            'suncare' => 'brightening-spf.jpg',
            'gentle-cleansers' => 'cleanser.jpg',
            'exfoliators' => 'night-cream.jpg',
            'face' => 'hero-shelf.jpg',
            'lips' => 'lipstick.jpg',
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
