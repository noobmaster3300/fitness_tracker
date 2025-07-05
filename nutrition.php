<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Nutrition & Recipes - Fitness Tracker</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }
        h2 { 
            text-align: center; 
            font-size: 2rem; 
            font-weight: bold;
            margin-bottom: 30px; 
            color: #333;
        }
        .category-buttons { 
            display: flex; 
            justify-content: center; 
            flex-wrap: wrap; 
            gap: 10px; 
            margin-bottom: 30px; 
        }
        .category-buttons button { 
            padding: 10px 20px; 
            border: none; 
            border-radius: 4px; 
            background-color: #007bff;
            color: white;
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer; 
        }
        .category-buttons button:hover { 
            background-color: #0056b3;
        }
        .category-buttons button.active { 
            background-color: #28a745;
        }
        .recipe-container { 
            display: none; 
        }
        .recipe-container.active { 
            display: block; 
        }
        .section-title { 
            font-size: 1.5rem; 
            margin: 30px 0 20px; 
            text-align: center;
            font-weight: bold;
            color: #333;
        }
        .recipe-card { 
            background: #f8f9fa; 
            border-radius: 8px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin: 20px auto; 
            max-width: 750px; 
            overflow: hidden; 
            border: 1px solid #dee2e6;
        }
        .recipe-card img { 
            width: 100%; 
            height: 250px; 
            object-fit: cover;
        }
        .recipe-card-content { 
            padding: 25px; 
        }
        .recipe-card h3, .recipe-card h4 { 
            margin: 0 0 15px 0;
            font-size: 1.3rem;
            font-weight: bold;
            color: #333;
        }
        .nutrition { 
            font-weight: bold; 
            margin: 15px 0; 
            color: #dc3545;
            font-size: 1rem;
            padding: 10px 15px;
            background: #f8d7da;
            border-radius: 4px;
            display: inline-block;
            border-left: 4px solid #dc3545;
        }
        .recipe-card p {
            margin: 15px 0;
            line-height: 1.6;
            color: #555;
            font-size: 1rem;
        }
        .recipe-card strong {
            color: #333;
            font-weight: bold;
        }
        .recipe-card em {
            color: #28a745;
            font-style: italic;
            font-weight: bold;
        }
        .recipe-card ol {
            margin: 15px 0;
            padding-left: 20px;
        }
        .recipe-card li {
            margin: 8px 0;
            color: #555;
            line-height: 1.5;
            font-size: 1rem;
        }
        .home-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .home-btn:hover {
            background-color: #545b62;
        }
        @media (max-width: 768px) {
            .category-buttons {
                flex-direction: column;
                align-items: center;
            }
            .recipe-card-content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<a href="dashboard.php" class="home-btn">← Back to Dashboard</a>

<div class="container">
    <h2>Nutrition & Recipes Guide</h2>
    
    <div class="category-buttons">
        <button onclick="showCategory('breakfast')" class="active">Breakfast</button>
        <button onclick="showCategory('lunch')">Lunch</button>
        <button onclick="showCategory('dinner')">Dinner</button>
        <button onclick="showCategory('snacks')">Snacks</button>
        <button onclick="showCategory('smoothies')">Smoothies</button>
    </div>

    <!-- Breakfast Section -->
    <div id="breakfast" class="recipe-container active">
        <h3 class="section-title">Healthy Breakfast Recipes</h3>
        
        <div class="recipe-card">
            <img src="images/overnight_oats.jpg" alt="Overnight Oats">
            <div class="recipe-card-content">
                <h3>Overnight Oats with Berries</h3>
                <div class="nutrition">Calories: 320 | Protein: 12g | Carbs: 45g | Fat: 8g</div>
                <p><strong>Ingredients:</strong> 1/2 cup rolled oats, 1/2 cup almond milk, 1 tbsp chia seeds, 1 tbsp honey, mixed berries</p>
                <p><strong>Instructions:</strong></p>
                <ol>
                    <li>Mix oats, almond milk, chia seeds, and honey in a jar</li>
                    <li>Refrigerate overnight (8+ hours)</li>
                    <li>Top with fresh berries before serving</li>
                </ol>
                <p><em>Tip: Add nuts or seeds for extra protein and healthy fats.</em></p>
            </div>
        </div>

        <div class="recipe-card">
            <img src="images/greek_yogurt_parfait.jpg" alt="Greek Yogurt Parfait">
            <div class="recipe-card-content">
                <h3>Greek Yogurt Parfait</h3>
                <div class="nutrition">Calories: 280 | Protein: 18g | Carbs: 35g | Fat: 6g</div>
                <p><strong>Ingredients:</strong> 1 cup Greek yogurt, 1/4 cup granola, 1/2 cup mixed berries, 1 tbsp honey</p>
                <p><strong>Instructions:</strong></p>
                <ol>
                    <li>Layer Greek yogurt in a glass</li>
                    <li>Add granola layer</li>
                    <li>Top with berries and drizzle honey</li>
                </ol>
                <p><em>High in protein and probiotics for gut health.</em></p>
            </div>
        </div>
    </div>

    <!-- Lunch Section -->
    <div id="lunch" class="recipe-container">
        <h3 class="section-title">Nutritious Lunch Options</h3>
        
        <div class="recipe-card">
            <img src="images/quinoa_salad.jpg" alt="Quinoa Salad">
            <div class="recipe-card-content">
                <h3>Mediterranean Quinoa Salad</h3>
                <div class="nutrition">Calories: 380 | Protein: 14g | Carbs: 42g | Fat: 18g</div>
                <p><strong>Ingredients:</strong> 1 cup cooked quinoa, cherry tomatoes, cucumber, olives, feta cheese, olive oil, lemon juice</p>
                <p><strong>Instructions:</strong></p>
                <ol>
                    <li>Cook quinoa according to package instructions</li>
                    <li>Chop vegetables and mix with quinoa</li>
                    <li>Add olives and feta cheese</li>
                    <li>Dress with olive oil and lemon juice</li>
                </ol>
                <p><em>Rich in fiber, protein, and healthy fats.</em></p>
            </div>
        </div>

        <div class="recipe-card">
            <img src="images/grilled_chicken_salad.jpg" alt="Grilled Chicken Salad">
            <div class="recipe-card-content">
                <h3>Grilled Chicken Salad</h3>
                <div class="nutrition">Calories: 320 | Protein: 35g | Carbs: 15g | Fat: 12g</div>
                <p><strong>Ingredients:</strong> Grilled chicken breast, mixed greens, avocado, tomatoes, balsamic vinaigrette</p>
                <p><strong>Instructions:</strong></p>
                <ol>
                    <li>Grill chicken breast until cooked through</li>
                    <li>Slice chicken and arrange on mixed greens</li>
                    <li>Add sliced avocado and tomatoes</li>
                    <li>Drizzle with balsamic vinaigrette</li>
                </ol>
                <p><em>High protein meal perfect for muscle building.</em></p>
            </div>
        </div>
    </div>

    <!-- Dinner Section -->
    <div id="dinner" class="recipe-container">
        <h3 class="section-title">Healthy Dinner Recipes</h3>
        
        <div class="recipe-card">
            <img src="images/salmon_vegetables.jpg" alt="Salmon with Vegetables">
            <div class="recipe-card-content">
                <h3>Baked Salmon with Roasted Vegetables</h3>
                <div class="nutrition">Calories: 420 | Protein: 38g | Carbs: 25g | Fat: 22g</div>
                <p><strong>Ingredients:</strong> Salmon fillet, broccoli, carrots, olive oil, herbs, lemon</p>
                <p><strong>Instructions:</strong></p>
                <ol>
                    <li>Preheat oven to 400°F (200°C)</li>
                    <li>Place salmon on baking sheet with vegetables</li>
                    <li>Drizzle with olive oil and season with herbs</li>
                    <li>Bake for 20-25 minutes until salmon flakes easily</li>
                </ol>
                <p><em>Rich in omega-3 fatty acids and lean protein.</em></p>
            </div>
        </div>

        <div class="recipe-card">
            <img src="images/stir_fry.jpg" alt="Vegetable Stir Fry">
            <div class="recipe-card-content">
                <h3>Vegetable Stir Fry with Tofu</h3>
                <div class="nutrition">Calories: 350 | Protein: 16g | Carbs: 38g | Fat: 14g</div>
                <p><strong>Ingredients:</strong> Tofu, broccoli, bell peppers, soy sauce, ginger, garlic, brown rice</p>
                <p><strong>Instructions:</strong></p>
                <ol>
                    <li>Cook brown rice according to package instructions</li>
                    <li>Stir fry tofu until golden</li>
                    <li>Add vegetables and stir fry until tender</li>
                    <li>Season with soy sauce, ginger, and garlic</li>
                </ol>
                <p><em>Plant-based protein with plenty of vegetables.</em></p>
            </div>
        </div>
    </div>

    <!-- Snacks Section -->
    <div id="snacks" class="recipe-container">
        <h3 class="section-title">Healthy Snack Ideas</h3>
        
        <div class="recipe-card">
            <img src="images/nut_mix.jpg" alt="Mixed Nuts">
            <div class="recipe-card-content">
                <h3>Trail Mix</h3>
                <div class="nutrition">Calories: 160 (per 1/4 cup) | Protein: 6g | Carbs: 12g | Fat: 12g</div>
                <p><strong>Ingredients:</strong> Almonds, walnuts, dried cranberries, dark chocolate chips</p>
                <p><strong>Benefits:</strong> Provides healthy fats, protein, and antioxidants. Perfect for pre or post-workout energy.</p>
                <p><em>Portion control is key - stick to 1/4 cup servings.</em></p>
            </div>
        </div>

        <div class="recipe-card">
            <img src="images/apple_peanut_butter.jpg" alt="Apple with Peanut Butter">
            <div class="recipe-card-content">
                <h3>Apple Slices with Peanut Butter</h3>
                <div class="nutrition">Calories: 200 | Protein: 8g | Carbs: 25g | Fat: 10g</div>
                <p><strong>Ingredients:</strong> 1 medium apple, 2 tbsp natural peanut butter</p>
                <p><strong>Benefits:</strong> Combines fiber from apple with protein and healthy fats from peanut butter.</p>
                <p><em>Choose natural peanut butter without added sugars.</em></p>
            </div>
        </div>
    </div>

    <!-- Smoothies Section -->
    <div id="smoothies" class="recipe-container">
        <h3 class="section-title">Nutritious Smoothies</h3>
        
        <div class="recipe-card">
            <img src="images/berry_smoothie.jpg" alt="Berry Smoothie">
            <div class="recipe-card-content">
                <h3>Berry Protein Smoothie</h3>
                <div class="nutrition">Calories: 280 | Protein: 25g | Carbs: 35g | Fat: 4g</div>
                <p><strong>Ingredients:</strong> 1 cup mixed berries, 1 scoop protein powder, 1 cup almond milk, 1 tbsp chia seeds</p>
                <p><strong>Instructions:</strong></p>
                <ol>
                    <li>Add all ingredients to blender</li>
                    <li>Blend until smooth</li>
                    <li>Add ice if desired for colder smoothie</li>
                </ol>
                <p><em>Perfect post-workout recovery drink.</em></p>
            </div>
        </div>

        <div class="recipe-card">
            <img src="images/green_smoothie.jpg" alt="Green Smoothie">
            <div class="recipe-card-content">
                <h3>Green Detox Smoothie</h3>
                <div class="nutrition">Calories: 180 | Protein: 8g | Carbs: 28g | Fat: 6g</div>
                <p><strong>Ingredients:</strong> 2 cups spinach, 1 banana, 1 cup pineapple, 1 cup coconut water</p>
                <p><strong>Instructions:</strong></p>
                <ol>
                    <li>Blend spinach and coconut water first</li>
                    <li>Add banana and pineapple</li>
                    <li>Blend until smooth and creamy</li>
                </ol>
                <p><em>Packed with vitamins, minerals, and antioxidants.</em></p>
            </div>
        </div>
    </div>
</div>

<script>
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
}
</script>

</body>
</html>
