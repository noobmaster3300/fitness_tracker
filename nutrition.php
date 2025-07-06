<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

function image_or_placeholder($img) {
    // Check in nutrition_food folder first
    $nutrition_food_path = __DIR__ . '/nutrition_food/' . $img;
    if (file_exists($nutrition_food_path)) {
        return 'nutrition_food/' . $img;
    }
    
    // Fallback to images folder
    $img_path = __DIR__ . '/images/' . $img;
    if (file_exists($img_path)) {
        return 'images/' . $img;
    } else {
        return 'images/placeholder_food.jpg';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Nutrition & Recipes - Fitness Tracker</title>
    <link rel="stylesheet" href="css/shared.css">
    <style>
        .category-buttons { display: flex; justify-content: center; flex-wrap: wrap; gap: 10px; margin-bottom: 30px; }
        .category-buttons button { padding: 10px 20px; border: none; border-radius: 4px; background-color: #007bff; color: white; font-weight: bold; font-size: 1rem; cursor: pointer; }
        .category-buttons button:hover { background-color: #0056b3; }
        .category-buttons button.active { background-color: #28a745; }
        .recipe-container { display: none; }
        .recipe-container.active { display: block; }
        .section-title { font-size: 1.5rem; margin: 30px 0 20px; text-align: center; font-weight: bold; color: #333; }
        .recipe-card { background: #f8f9fa; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin: 20px auto; max-width: 750px; overflow: hidden; border: 1px solid #dee2e6; display: flex; align-items: flex-start; }
        .recipe-card img { width: 200px; height: 200px; object-fit: cover; flex-shrink: 0; border-radius: 12px; border: 3px solid #e9ecef; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .recipe-card-content { padding: 25px; flex: 1; }
        .recipe-card h3, .recipe-card h4 { margin: 0 0 15px 0; font-size: 1.3rem; font-weight: bold; color: #333; }
        .nutrition { font-weight: bold; margin: 15px 0; color: #dc3545; font-size: 1rem; padding: 10px 15px; background: #f8d7da; border-radius: 4px; display: inline-block; border-left: 4px solid #dc3545; }
        .recipe-card p { margin: 15px 0; line-height: 1.6; color: #555; font-size: 1rem; }
        .recipe-card strong { color: #333; font-weight: bold; }
        .recipe-card em { color: #28a745; font-style: italic; font-weight: bold; }
        .recipe-card ol { margin: 15px 0; padding-left: 20px; }
        .recipe-card li { margin: 8px 0; color: #555; line-height: 1.5; font-size: 1rem; }
        @media (max-width: 768px) { .category-buttons { flex-direction: column; align-items: center; } .recipe-card { flex-direction: column; } .recipe-card img { width: 100%; height: 200px; } .recipe-card-content { padding: 20px; } }
    </style>
</head>
<body>
<div class="container">
    <div class="page-header">
        <a href="dashboard.php" class="back-btn" title="Back to Dashboard">&#8592;</a>
        <div class="title-section">
            <h1>Nutrition & Recipes Guide</h1>
            <div class="subtitle">Discover healthy recipes and nutrition tips</div>
        </div>
    </div>
    <div class="card mb-20">
        <div class="sort-controls" style="text-align:center; margin-bottom:20px;">
            <label for="sortSelect"><strong>Sort by:</strong></label>
            <select id="sortSelect" onchange="sortRecipes()">
                <option value="default">Default</option>
                <option value="protein">Most Protein</option>
                <option value="calories">Most Calories</option>
                <option value="carbs">Most Carbs</option>
                <option value="fat">Most Fat</option>
            </select>
        </div>
        <div class="category-buttons">
            <button class="active" onclick="showCategory('all')">All</button>
            <button onclick="showCategory('breakfast')">Breakfast</button>
            <button onclick="showCategory('lunch')">Lunch</button>
            <button onclick="showCategory('dinner')">Dinner</button>
            <button onclick="showCategory('snacks')">Snacks</button>
        </div>
        <div id="recipes">
            <div id="all" class="recipe-container active">
                <!-- BREAKFAST RECIPES -->
                <div class="recipe-card" data-protein="7" data-calories="220" data-carbs="44" data-fat="2">
                    <img src="<?php echo image_or_placeholder('idili.jpg'); ?>" alt="Idli with Sambar">
                    <div class="recipe-card-content">
                        <h3>Idli with Sambar</h3>
                        <div class="nutrition">Calories: 220 | Protein: 7g | Carbs: 44g | Fat: 2g</div>
                        <p><strong>Ingredients:</strong> Idli (fermented rice and urad dal), sambar (toor dal, mixed vegetables, tamarind, sambar powder).</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Steam idlis using idli batter.</li>
                            <li>Cook dal and vegetables with spices for sambar.</li>
                            <li>Serve hot idlis with sambar.</li>
                        </ol>
                        <p><em>Tip: Fermented foods like idli are great for gut health.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> Light, protein-rich breakfast.</span></p>
                    </div>
                </div>
                <div class="recipe-card" data-protein="6" data-calories="250" data-carbs="50" data-fat="4">
                    <img src="<?php echo image_or_placeholder('poha.jpg'); ?>" alt="Poha">
                    <div class="recipe-card-content">
                        <h3>Poha (Flattened Rice Upma)</h3>
                        <div class="nutrition">Calories: 250 | Protein: 6g | Carbs: 50g | Fat: 4g</div>
                        <p><strong>Ingredients:</strong> Flattened rice (poha), onions, peas, peanuts, curry leaves, turmeric, lemon.</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Rinse poha and drain well.</li>
                            <li>Sauté onions, peas, and peanuts with curry leaves and turmeric.</li>
                            <li>Add poha, cook for 2-3 minutes, finish with lemon juice.</li>
                        </ol>
                        <p><em>Tip: Poha is light, easy to digest, and a good source of iron and carbs.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> Breakfast, light dinner, kids' tiffin.</span></p>
                    </div>
                </div>
                <div class="recipe-card" data-protein="8" data-calories="280" data-carbs="45" data-fat="6">
                    <img src="<?php echo image_or_placeholder('upma.jpg'); ?>" alt="Upma">
                    <div class="recipe-card-content">
                        <h3>Upma (Semolina Breakfast)</h3>
                        <div class="nutrition">Calories: 280 | Protein: 8g | Carbs: 45g | Fat: 6g</div>
                        <p><strong>Ingredients:</strong> Semolina (rava), onions, carrots, peas, curry leaves, mustard seeds, ghee.</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Dry roast semolina until golden brown.</li>
                            <li>Temper mustard seeds, curry leaves, and vegetables.</li>
                            <li>Add water and semolina, cook until fluffy.</li>
                        </ol>
                        <p><em>Tip: Upma is a quick, nutritious breakfast that keeps you full for hours.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> Quick breakfast, office lunch.</span></p>
                    </div>
                </div>
                <div class="recipe-card" data-protein="12" data-calories="320" data-carbs="35" data-fat="8">
                    <img src="<?php echo image_or_placeholder('overnight_oats.jpg'); ?>" alt="Overnight Oats">
                    <div class="recipe-card-content">
                        <h3>Overnight Oats with Berries</h3>
                        <div class="nutrition">Calories: 320 | Protein: 12g | Carbs: 35g | Fat: 8g</div>
                        <p><strong>Ingredients:</strong> Rolled oats, milk, yogurt, honey, mixed berries, chia seeds, nuts.</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Mix oats, milk, yogurt, and honey in a jar.</li>
                            <li>Refrigerate overnight.</li>
                            <li>Top with berries, chia seeds, and nuts before serving.</li>
                        </ol>
                        <p><em>Tip: Overnight oats are perfect for meal prep and provide sustained energy.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> Healthy breakfast, post-workout meal.</span></p>
                    </div>
                </div>
                <div class="recipe-card" data-protein="15" data-calories="350" data-carbs="30" data-fat="12">
                    <img src="<?php echo image_or_placeholder('greek_yogurt_parfait.jpg'); ?>" alt="Greek Yogurt Parfait">
                    <div class="recipe-card-content">
                        <h3>Greek Yogurt Parfait</h3>
                        <div class="nutrition">Calories: 350 | Protein: 15g | Carbs: 30g | Fat: 12g</div>
                        <p><strong>Ingredients:</strong> Greek yogurt, granola, mixed berries, honey, almonds.</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Layer Greek yogurt in a glass.</li>
                            <li>Add granola and berries in layers.</li>
                            <li>Drizzle with honey and top with almonds.</li>
                        </ol>
                        <p><em>Tip: Greek yogurt is high in protein and probiotics for gut health.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> Protein-rich breakfast, healthy dessert.</span></p>
                    </div>
                </div>

                <!-- LUNCH RECIPES -->
                <div class="recipe-card" data-protein="18" data-calories="420" data-carbs="55" data-fat="15">
                    <img src="<?php echo image_or_placeholder('quinoa_salad.jpg'); ?>" alt="Quinoa Salad">
                    <div class="recipe-card-content">
                        <h3>Quinoa Vegetable Salad</h3>
                        <div class="nutrition">Calories: 420 | Protein: 18g | Carbs: 55g | Fat: 15g</div>
                        <p><strong>Ingredients:</strong> Quinoa, mixed vegetables, olive oil, lemon juice, herbs, feta cheese.</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Cook quinoa according to package instructions.</li>
                            <li>Chop and prepare fresh vegetables.</li>
                            <li>Mix with olive oil, lemon juice, and herbs.</li>
                        </ol>
                        <p><em>Tip: Quinoa is a complete protein and gluten-free grain.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> Healthy lunch, vegetarian protein source.</span></p>
                    </div>
                </div>
                <div class="recipe-card" data-protein="25" data-calories="450" data-carbs="40" data-fat="18">
                    <img src="<?php echo image_or_placeholder('grilled_chicken_salad.jpg'); ?>" alt="Grilled Chicken Salad">
                    <div class="recipe-card-content">
                        <h3>Grilled Chicken Salad</h3>
                        <div class="nutrition">Calories: 450 | Protein: 25g | Carbs: 40g | Fat: 18g</div>
                        <p><strong>Ingredients:</strong> Chicken breast, mixed greens, cherry tomatoes, cucumber, olive oil, balsamic vinegar.</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Grill chicken breast with herbs and spices.</li>
                            <li>Prepare fresh salad greens and vegetables.</li>
                            <li>Slice chicken and serve over salad with dressing.</li>
                        </ol>
                        <p><em>Tip: Grilled chicken is lean protein that helps with muscle building.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> High-protein lunch, post-workout meal.</span></p>
                    </div>
                </div>
                <div class="recipe-card" data-protein="12" data-calories="380" data-carbs="65" data-fat="10">
                    <img src="<?php echo image_or_placeholder('buddha_bowl.jpg'); ?>" alt="Buddha Bowl">
                    <div class="recipe-card-content">
                        <h3>Buddha Bowl</h3>
                        <div class="nutrition">Calories: 380 | Protein: 12g | Carbs: 65g | Fat: 10g</div>
                        <p><strong>Ingredients:</strong> Brown rice, roasted vegetables, avocado, chickpeas, tahini sauce, seeds.</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Cook brown rice and roast vegetables.</li>
                            <li>Arrange rice, vegetables, and chickpeas in a bowl.</li>
                            <li>Top with avocado, tahini sauce, and seeds.</li>
                        </ol>
                        <p><em>Tip: Buddha bowls are balanced meals with complex carbs, protein, and healthy fats.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> Balanced lunch, vegetarian meal.</span></p>
                    </div>
                </div>
                <div class="recipe-card" data-protein="20" data-calories="400" data-carbs="45" data-fat="16">
                    <img src="<?php echo image_or_placeholder('tuna_chickpea_salad.jpg'); ?>" alt="Tuna Chickpea Salad">
                    <div class="recipe-card-content">
                        <h3>Tuna Chickpea Salad</h3>
                        <div class="nutrition">Calories: 400 | Protein: 20g | Carbs: 45g | Fat: 16g</div>
                        <p><strong>Ingredients:</strong> Tuna, chickpeas, red onion, celery, olive oil, lemon juice, herbs.</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Drain and flake tuna.</li>
                            <li>Mix with chickpeas, chopped vegetables, and herbs.</li>
                            <li>Dress with olive oil and lemon juice.</li>
                        </ol>
                        <p><em>Tip: Tuna is rich in omega-3 fatty acids and lean protein.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> Quick lunch, high-protein meal.</span></p>
                    </div>
                </div>

                <!-- DINNER RECIPES -->
                <div class="recipe-card" data-protein="22" data-calories="480" data-carbs="50" data-fat="20">
                    <img src="<?php echo image_or_placeholder('baked_salmon.jpg'); ?>" alt="Baked Salmon">
                    <div class="recipe-card-content">
                        <h3>Baked Salmon with Vegetables</h3>
                        <div class="nutrition">Calories: 480 | Protein: 22g | Carbs: 50g | Fat: 20g</div>
                        <p><strong>Ingredients:</strong> Salmon fillet, mixed vegetables, olive oil, herbs, lemon, garlic.</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Season salmon with herbs, lemon, and garlic.</li>
                            <li>Arrange vegetables around salmon on baking sheet.</li>
                            <li>Bake at 400°F for 15-20 minutes.</li>
                        </ol>
                        <p><em>Tip: Salmon is excellent for heart health and brain function.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> Healthy dinner, omega-3 rich meal.</span></p>
                    </div>
                </div>
                <div class="recipe-card" data-protein="18" data-calories="420" data-carbs="60" data-fat="14">
                    <img src="<?php echo image_or_placeholder('vegetable_stir_fry.jpg'); ?>" alt="Vegetable Stir Fry">
                    <div class="recipe-card-content">
                        <h3>Vegetable Stir Fry</h3>
                        <div class="nutrition">Calories: 420 | Protein: 18g | Carbs: 60g | Fat: 14g</div>
                        <p><strong>Ingredients:</strong> Mixed vegetables, tofu, soy sauce, ginger, garlic, sesame oil, brown rice.</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Cook brown rice according to package instructions.</li>
                            <li>Stir fry vegetables and tofu with ginger and garlic.</li>
                            <li>Add soy sauce and serve over rice.</li>
                        </ol>
                        <p><em>Tip: Stir frying preserves nutrients and creates delicious flavors.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> Quick dinner, vegetarian protein.</span></p>
                    </div>
                </div>
                <div class="recipe-card" data-protein="24" data-calories="460" data-carbs="45" data-fat="18">
                    <img src="<?php echo image_or_placeholder('chicken_quinoa_bowl.jpg'); ?>" alt="Chicken Quinoa Bowl">
                    <div class="recipe-card-content">
                        <h3>Chicken Quinoa Bowl</h3>
                        <div class="nutrition">Calories: 460 | Protein: 24g | Carbs: 45g | Fat: 18g</div>
                        <p><strong>Ingredients:</strong> Chicken breast, quinoa, roasted vegetables, avocado, tahini sauce.</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Cook quinoa and grill chicken breast.</li>
                            <li>Roast vegetables until tender.</li>
                            <li>Assemble bowl with quinoa, chicken, vegetables, and sauce.</li>
                        </ol>
                        <p><em>Tip: This bowl provides complete protein and complex carbohydrates.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> Balanced dinner, post-workout recovery.</span></p>
                    </div>
                </div>
                <div class="recipe-card" data-protein="16" data-calories="380" data-carbs="55" data-fat="12">
                    <img src="<?php echo image_or_placeholder('chickpea_spinach_curry.jpg'); ?>" alt="Chickpea Spinach Curry">
                    <div class="recipe-card-content">
                        <h3>Chickpea Spinach Curry</h3>
                        <div class="nutrition">Calories: 380 | Protein: 16g | Carbs: 55g | Fat: 12g</div>
                        <p><strong>Ingredients:</strong> Chickpeas, spinach, tomatoes, onions, spices, coconut milk, brown rice.</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Sauté onions and spices until fragrant.</li>
                            <li>Add chickpeas, tomatoes, and coconut milk.</li>
                            <li>Simmer until thickened, add spinach at the end.</li>
                        </ol>
                        <p><em>Tip: This curry is rich in fiber, protein, and iron from spinach.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> Vegetarian dinner, iron-rich meal.</span></p>
                    </div>
                </div>

                <!-- SNACKS RECIPES -->
                <div class="recipe-card" data-protein="8" data-calories="180" data-carbs="25" data-fat="8">
                    <img src="<?php echo image_or_placeholder('hummus_veggies.jpg'); ?>" alt="Hummus with Vegetables">
                    <div class="recipe-card-content">
                        <h3>Hummus with Fresh Vegetables</h3>
                        <div class="nutrition">Calories: 180 | Protein: 8g | Carbs: 25g | Fat: 8g</div>
                        <p><strong>Ingredients:</strong> Chickpeas, tahini, olive oil, lemon juice, garlic, fresh vegetables.</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Blend chickpeas, tahini, olive oil, lemon juice, and garlic.</li>
                            <li>Serve with fresh cut vegetables.</li>
                            <li>Garnish with olive oil and paprika.</li>
                        </ol>
                        <p><em>Tip: Hummus is a great source of plant-based protein and fiber.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> Healthy snack, pre-workout fuel.</span></p>
                    </div>
                </div>
                <div class="recipe-card" data-protein="6" data-calories="150" data-carbs="20" data-fat="6">
                    <img src="<?php echo image_or_placeholder('nut_mix.jpg'); ?>" alt="Mixed Nuts">
                    <div class="recipe-card-content">
                        <h3>Mixed Nuts and Seeds</h3>
                        <div class="nutrition">Calories: 150 | Protein: 6g | Carbs: 20g | Fat: 6g</div>
                        <p><strong>Ingredients:</strong> Almonds, walnuts, pumpkin seeds, sunflower seeds, dried fruits.</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Mix raw or roasted nuts and seeds.</li>
                            <li>Add small amount of dried fruits for sweetness.</li>
                            <li>Store in an airtight container.</li>
                        </ol>
                        <p><em>Tip: Nuts provide healthy fats, protein, and essential minerals.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> Energy-boosting snack, healthy fats.</span></p>
                    </div>
                </div>
                <div class="recipe-card" data-protein="10" data-calories="200" data-carbs="30" data-fat="7">
                    <img src="<?php echo image_or_placeholder('pb_energy_balls.jpg'); ?>" alt="Peanut Butter Energy Balls">
                    <div class="recipe-card-content">
                        <h3>Peanut Butter Energy Balls</h3>
                        <div class="nutrition">Calories: 200 | Protein: 10g | Carbs: 30g | Fat: 7g</div>
                        <p><strong>Ingredients:</strong> Dates, peanut butter, oats, chia seeds, honey, coconut flakes.</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Process dates in food processor until paste forms.</li>
                            <li>Mix with peanut butter, oats, and seeds.</li>
                            <li>Roll into balls and coat with coconut flakes.</li>
                        </ol>
                        <p><em>Tip: These energy balls are perfect for pre or post-workout snacks.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> Pre-workout snack, natural energy boost.</span></p>
                    </div>
                </div>
                <div class="recipe-card" data-protein="5" data-calories="120" data-carbs="15" data-fat="5">
                    <img src="<?php echo image_or_placeholder('apple_peanut_butter.jpg'); ?>" alt="Apple with Peanut Butter">
                    <div class="recipe-card-content">
                        <h3>Apple Slices with Peanut Butter</h3>
                        <div class="nutrition">Calories: 120 | Protein: 5g | Carbs: 15g | Fat: 5g</div>
                        <p><strong>Ingredients:</strong> Fresh apple, natural peanut butter, cinnamon (optional).</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Wash and slice apple into wedges.</li>
                            <li>Spread natural peanut butter on each slice.</li>
                            <li>Sprinkle with cinnamon if desired.</li>
                        </ol>
                        <p><em>Tip: This combination provides fiber, protein, and natural sweetness.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> Quick snack, natural energy source.</span></p>
                    </div>
                </div>
            </div>
            <div id="breakfast" class="recipe-container">
                <div class="recipe-card" data-protein="7" data-calories="220" data-carbs="44" data-fat="2">
                    <img src="<?php echo image_or_placeholder('idili.jpg'); ?>" alt="Idli with Sambar">
                    <div class="recipe-card-content">
                        <h3>Idli with Sambar</h3>
                        <div class="nutrition">Calories: 220 | Protein: 7g | Carbs: 44g | Fat: 2g</div>
                        <p><strong>Ingredients:</strong> Idli (fermented rice and urad dal), sambar (toor dal, mixed vegetables, tamarind, sambar powder).</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Steam idlis using idli batter.</li>
                            <li>Cook dal and vegetables with spices for sambar.</li>
                            <li>Serve hot idlis with sambar.</li>
                        </ol>
                        <p><em>Tip: Fermented foods like idli are great for gut health.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> Light, protein-rich breakfast.</span></p>
                    </div>
                </div>
                <div class="recipe-card" data-protein="6" data-calories="250" data-carbs="50" data-fat="4">
                    <img src="<?php echo image_or_placeholder('poha.jpg'); ?>" alt="Poha">
                    <div class="recipe-card-content">
                        <h3>Poha (Flattened Rice Upma)</h3>
                        <div class="nutrition">Calories: 250 | Protein: 6g | Carbs: 50g | Fat: 4g</div>
                        <p><strong>Ingredients:</strong> Flattened rice (poha), onions, peas, peanuts, curry leaves, turmeric, lemon.</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Rinse poha and drain well.</li>
                            <li>Sauté onions, peas, and peanuts with curry leaves and turmeric.</li>
                            <li>Add poha, cook for 2-3 minutes, finish with lemon juice.</li>
                        </ol>
                        <p><em>Tip: Poha is light, easy to digest, and a good source of iron and carbs.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> Breakfast, light dinner, kids' tiffin.</span></p>
                    </div>
                </div>
                <div class="recipe-card" data-protein="8" data-calories="280" data-carbs="45" data-fat="6">
                    <img src="<?php echo image_or_placeholder('upma.jpg'); ?>" alt="Upma">
                    <div class="recipe-card-content">
                        <h3>Upma (Semolina Breakfast)</h3>
                        <div class="nutrition">Calories: 280 | Protein: 8g | Carbs: 45g | Fat: 6g</div>
                        <p><strong>Ingredients:</strong> Semolina (rava), onions, carrots, peas, curry leaves, mustard seeds, ghee.</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Dry roast semolina until golden brown.</li>
                            <li>Temper mustard seeds, curry leaves, and vegetables.</li>
                            <li>Add water and semolina, cook until fluffy.</li>
                        </ol>
                        <p><em>Tip: Upma is a quick, nutritious breakfast that keeps you full for hours.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> Quick breakfast, office lunch.</span></p>
                    </div>
                </div>
                <div class="recipe-card" data-protein="12" data-calories="320" data-carbs="35" data-fat="8">
                    <img src="<?php echo image_or_placeholder('overnight_oats.jpg'); ?>" alt="Overnight Oats">
                    <div class="recipe-card-content">
                        <h3>Overnight Oats with Berries</h3>
                        <div class="nutrition">Calories: 320 | Protein: 12g | Carbs: 35g | Fat: 8g</div>
                        <p><strong>Ingredients:</strong> Rolled oats, milk, yogurt, honey, mixed berries, chia seeds, nuts.</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Mix oats, milk, yogurt, and honey in a jar.</li>
                            <li>Refrigerate overnight.</li>
                            <li>Top with berries, chia seeds, and nuts before serving.</li>
                        </ol>
                        <p><em>Tip: Overnight oats are perfect for meal prep and provide sustained energy.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> Healthy breakfast, post-workout meal.</span></p>
                    </div>
                </div>
                <div class="recipe-card" data-protein="15" data-calories="350" data-carbs="30" data-fat="12">
                    <img src="<?php echo image_or_placeholder('greek_yogurt_parfait.jpg'); ?>" alt="Greek Yogurt Parfait">
                    <div class="recipe-card-content">
                        <h3>Greek Yogurt Parfait</h3>
                        <div class="nutrition">Calories: 350 | Protein: 15g | Carbs: 30g | Fat: 12g</div>
                        <p><strong>Ingredients:</strong> Greek yogurt, granola, mixed berries, honey, almonds.</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Layer Greek yogurt in a glass.</li>
                            <li>Add granola and berries in layers.</li>
                            <li>Drizzle with honey and top with almonds.</li>
                        </ol>
                        <p><em>Tip: Greek yogurt is high in protein and probiotics for gut health.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> Protein-rich breakfast, healthy dessert.</span></p>
                    </div>
                </div>
            </div>
            <div id="lunch" class="recipe-container">
                <div class="recipe-card" data-protein="18" data-calories="420" data-carbs="55" data-fat="15">
                    <img src="<?php echo image_or_placeholder('quinoa_salad.jpg'); ?>" alt="Quinoa Salad">
                    <div class="recipe-card-content">
                        <h3>Quinoa Vegetable Salad</h3>
                        <div class="nutrition">Calories: 420 | Protein: 18g | Carbs: 55g | Fat: 15g</div>
                        <p><strong>Ingredients:</strong> Quinoa, mixed vegetables, olive oil, lemon juice, herbs, feta cheese.</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Cook quinoa according to package instructions.</li>
                            <li>Chop and prepare fresh vegetables.</li>
                            <li>Mix with olive oil, lemon juice, and herbs.</li>
                        </ol>
                        <p><em>Tip: Quinoa is a complete protein and gluten-free grain.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> Healthy lunch, vegetarian protein source.</span></p>
                    </div>
                </div>
                <div class="recipe-card" data-protein="25" data-calories="450" data-carbs="40" data-fat="18">
                    <img src="<?php echo image_or_placeholder('grilled_chicken_salad.jpg'); ?>" alt="Grilled Chicken Salad">
                    <div class="recipe-card-content">
                        <h3>Grilled Chicken Salad</h3>
                        <div class="nutrition">Calories: 450 | Protein: 25g | Carbs: 40g | Fat: 18g</div>
                        <p><strong>Ingredients:</strong> Chicken breast, mixed greens, cherry tomatoes, cucumber, olive oil, balsamic vinegar.</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Grill chicken breast with herbs and spices.</li>
                            <li>Prepare fresh salad greens and vegetables.</li>
                            <li>Slice chicken and serve over salad with dressing.</li>
                        </ol>
                        <p><em>Tip: Grilled chicken is lean protein that helps with muscle building.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> High-protein lunch, post-workout meal.</span></p>
                    </div>
                </div>
                <div class="recipe-card" data-protein="12" data-calories="380" data-carbs="65" data-fat="10">
                    <img src="<?php echo image_or_placeholder('buddha_bowl.jpg'); ?>" alt="Buddha Bowl">
                    <div class="recipe-card-content">
                        <h3>Buddha Bowl</h3>
                        <div class="nutrition">Calories: 380 | Protein: 12g | Carbs: 65g | Fat: 10g</div>
                        <p><strong>Ingredients:</strong> Brown rice, roasted vegetables, avocado, chickpeas, tahini sauce, seeds.</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Cook brown rice and roast vegetables.</li>
                            <li>Arrange rice, vegetables, and chickpeas in a bowl.</li>
                            <li>Top with avocado, tahini sauce, and seeds.</li>
                        </ol>
                        <p><em>Tip: Buddha bowls are balanced meals with complex carbs, protein, and healthy fats.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> Balanced lunch, vegetarian meal.</span></p>
                    </div>
                </div>
                <div class="recipe-card" data-protein="20" data-calories="400" data-carbs="45" data-fat="16">
                    <img src="<?php echo image_or_placeholder('tuna_chickpea_salad.jpg'); ?>" alt="Tuna Chickpea Salad">
                    <div class="recipe-card-content">
                        <h3>Tuna Chickpea Salad</h3>
                        <div class="nutrition">Calories: 400 | Protein: 20g | Carbs: 45g | Fat: 16g</div>
                        <p><strong>Ingredients:</strong> Tuna, chickpeas, red onion, celery, olive oil, lemon juice, herbs.</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Drain and flake tuna.</li>
                            <li>Mix with chickpeas, chopped vegetables, and herbs.</li>
                            <li>Dress with olive oil and lemon juice.</li>
                        </ol>
                        <p><em>Tip: Tuna is rich in omega-3 fatty acids and lean protein.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> Quick lunch, high-protein meal.</span></p>
                    </div>
                </div>
            </div>
            <div id="dinner" class="recipe-container">
                <div class="recipe-card" data-protein="22" data-calories="480" data-carbs="50" data-fat="20">
                    <img src="<?php echo image_or_placeholder('baked_salmon.jpg'); ?>" alt="Baked Salmon">
                    <div class="recipe-card-content">
                        <h3>Baked Salmon with Vegetables</h3>
                        <div class="nutrition">Calories: 480 | Protein: 22g | Carbs: 50g | Fat: 20g</div>
                        <p><strong>Ingredients:</strong> Salmon fillet, mixed vegetables, olive oil, herbs, lemon, garlic.</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Season salmon with herbs, lemon, and garlic.</li>
                            <li>Arrange vegetables around salmon on baking sheet.</li>
                            <li>Bake at 400°F for 15-20 minutes.</li>
                        </ol>
                        <p><em>Tip: Salmon is excellent for heart health and brain function.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> Healthy dinner, omega-3 rich meal.</span></p>
                    </div>
                </div>
                <div class="recipe-card" data-protein="18" data-calories="420" data-carbs="60" data-fat="14">
                    <img src="<?php echo image_or_placeholder('vegetable_stir_fry.jpg'); ?>" alt="Vegetable Stir Fry">
                    <div class="recipe-card-content">
                        <h3>Vegetable Stir Fry</h3>
                        <div class="nutrition">Calories: 420 | Protein: 18g | Carbs: 60g | Fat: 14g</div>
                        <p><strong>Ingredients:</strong> Mixed vegetables, tofu, soy sauce, ginger, garlic, sesame oil, brown rice.</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Cook brown rice according to package instructions.</li>
                            <li>Stir fry vegetables and tofu with ginger and garlic.</li>
                            <li>Add soy sauce and serve over rice.</li>
                        </ol>
                        <p><em>Tip: Stir frying preserves nutrients and creates delicious flavors.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> Quick dinner, vegetarian protein.</span></p>
                    </div>
                </div>
                <div class="recipe-card" data-protein="24" data-calories="460" data-carbs="45" data-fat="18">
                    <img src="<?php echo image_or_placeholder('chicken_quinoa_bowl.jpg'); ?>" alt="Chicken Quinoa Bowl">
                    <div class="recipe-card-content">
                        <h3>Chicken Quinoa Bowl</h3>
                        <div class="nutrition">Calories: 460 | Protein: 24g | Carbs: 45g | Fat: 18g</div>
                        <p><strong>Ingredients:</strong> Chicken breast, quinoa, roasted vegetables, avocado, tahini sauce.</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Cook quinoa and grill chicken breast.</li>
                            <li>Roast vegetables until tender.</li>
                            <li>Assemble bowl with quinoa, chicken, vegetables, and sauce.</li>
                        </ol>
                        <p><em>Tip: This bowl provides complete protein and complex carbohydrates.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> Balanced dinner, post-workout recovery.</span></p>
                    </div>
                </div>
                <div class="recipe-card" data-protein="16" data-calories="380" data-carbs="55" data-fat="12">
                    <img src="<?php echo image_or_placeholder('chickpea_spinach_curry.jpg'); ?>" alt="Chickpea Spinach Curry">
                    <div class="recipe-card-content">
                        <h3>Chickpea Spinach Curry</h3>
                        <div class="nutrition">Calories: 380 | Protein: 16g | Carbs: 55g | Fat: 12g</div>
                        <p><strong>Ingredients:</strong> Chickpeas, spinach, tomatoes, onions, spices, coconut milk, brown rice.</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Sauté onions and spices until fragrant.</li>
                            <li>Add chickpeas, tomatoes, and coconut milk.</li>
                            <li>Simmer until thickened, add spinach at the end.</li>
                        </ol>
                        <p><em>Tip: This curry is rich in fiber, protein, and iron from spinach.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> Vegetarian dinner, iron-rich meal.</span></p>
                    </div>
                </div>
            </div>
            <div id="snacks" class="recipe-container">
                <div class="recipe-card" data-protein="8" data-calories="180" data-carbs="25" data-fat="8">
                    <img src="<?php echo image_or_placeholder('hummus_veggies.jpg'); ?>" alt="Hummus with Vegetables">
                    <div class="recipe-card-content">
                        <h3>Hummus with Fresh Vegetables</h3>
                        <div class="nutrition">Calories: 180 | Protein: 8g | Carbs: 25g | Fat: 8g</div>
                        <p><strong>Ingredients:</strong> Chickpeas, tahini, olive oil, lemon juice, garlic, fresh vegetables.</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Blend chickpeas, tahini, olive oil, lemon juice, and garlic.</li>
                            <li>Serve with fresh cut vegetables.</li>
                            <li>Garnish with olive oil and paprika.</li>
                        </ol>
                        <p><em>Tip: Hummus is a great source of plant-based protein and fiber.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> Healthy snack, pre-workout fuel.</span></p>
                    </div>
                </div>
                <div class="recipe-card" data-protein="6" data-calories="150" data-carbs="20" data-fat="6">
                    <img src="<?php echo image_or_placeholder('nut_mix.jpg'); ?>" alt="Mixed Nuts">
                    <div class="recipe-card-content">
                        <h3>Mixed Nuts and Seeds</h3>
                        <div class="nutrition">Calories: 150 | Protein: 6g | Carbs: 20g | Fat: 6g</div>
                        <p><strong>Ingredients:</strong> Almonds, walnuts, pumpkin seeds, sunflower seeds, dried fruits.</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Mix raw or roasted nuts and seeds.</li>
                            <li>Add small amount of dried fruits for sweetness.</li>
                            <li>Store in an airtight container.</li>
                        </ol>
                        <p><em>Tip: Nuts provide healthy fats, protein, and essential minerals.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> Energy-boosting snack, healthy fats.</span></p>
                    </div>
                </div>
                <div class="recipe-card" data-protein="10" data-calories="200" data-carbs="30" data-fat="7">
                    <img src="<?php echo image_or_placeholder('pb_energy_balls.jpg'); ?>" alt="Peanut Butter Energy Balls">
                    <div class="recipe-card-content">
                        <h3>Peanut Butter Energy Balls</h3>
                        <div class="nutrition">Calories: 200 | Protein: 10g | Carbs: 30g | Fat: 7g</div>
                        <p><strong>Ingredients:</strong> Dates, peanut butter, oats, chia seeds, honey, coconut flakes.</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Process dates in food processor until paste forms.</li>
                            <li>Mix with peanut butter, oats, and seeds.</li>
                            <li>Roll into balls and coat with coconut flakes.</li>
                        </ol>
                        <p><em>Tip: These energy balls are perfect for pre or post-workout snacks.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> Pre-workout snack, natural energy boost.</span></p>
                    </div>
                </div>
                <div class="recipe-card" data-protein="5" data-calories="120" data-carbs="15" data-fat="5">
                    <img src="<?php echo image_or_placeholder('apple_peanut_butter.jpg'); ?>" alt="Apple with Peanut Butter">
                    <div class="recipe-card-content">
                        <h3>Apple Slices with Peanut Butter</h3>
                        <div class="nutrition">Calories: 120 | Protein: 5g | Carbs: 15g | Fat: 5g</div>
                        <p><strong>Ingredients:</strong> Fresh apple, natural peanut butter, cinnamon (optional).</p>
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Wash and slice apple into wedges.</li>
                            <li>Spread natural peanut butter on each slice.</li>
                            <li>Sprinkle with cinnamon if desired.</li>
                        </ol>
                        <p><em>Tip: This combination provides fiber, protein, and natural sweetness.</em></p>
                        <p><span style="color:#28a745;"><strong>Best for:</strong> Quick snack, natural energy source.</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <h3 class="text-center">Nutrition Tips</h3>
        <ul>
            <li>Eat a variety of foods from all food groups.</li>
            <li>Stay hydrated—drink plenty of water throughout the day.</li>
            <li>Include protein, healthy fats, and fiber in every meal.</li>
            <li>Limit processed foods and added sugars.</li>
            <li>Plan your meals and snacks ahead of time.</li>
        </ul>
    </div>
</div>
<script>
function sortRecipes() {
    const sortBy = document.getElementById('sortSelect').value;
    const activeCategory = document.querySelector('.recipe-container.active');
    const cards = Array.from(activeCategory.querySelectorAll('.recipe-card'));

    if (sortBy === 'default') {
        cards.forEach(card => activeCategory.appendChild(card)); // original order
        return;
    }

    cards.sort((a, b) => {
        const aVal = parseFloat(a.dataset[sortBy]);
        const bVal = parseFloat(b.dataset[sortBy]);
        return bVal - aVal; // descending
    });

    cards.forEach(card => activeCategory.appendChild(card));
}

function showCategory(category) {
    // Hide all recipe containers
    const containers = document.querySelectorAll('.recipe-container');
    containers.forEach(container => {
        container.classList.remove('active');
    });
    
    // Show selected category
    document.getElementById(category).classList.add('active');
    
    // Update button states
    const buttons = document.querySelectorAll('.category-buttons button');
    buttons.forEach(button => {
        button.classList.remove('active');
    });
    event.target.classList.add('active');
    // Resort recipes in the new category
    sortRecipes();
}
</script>
</body>
</html>
