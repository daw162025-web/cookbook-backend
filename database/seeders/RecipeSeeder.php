<?php

namespace Database\Seeders;

use App\Models\Recipe;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'telma@gmail.com')->first() ?: User::factory()->create([
            'name' => 'Telma',
            'email' => 'telma@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
        ]);

        $recipesData = [
            [
                'title' => 'Paella Valenciana Auténtica',
                'description' => 'La verdadera paella valenciana con pollo, conejo y el toque justo de azafrán.',
                'instructions' => [
                    'Sofreír el pollo y el conejo en aceite de oliva hasta que estén dorados.',
                    'Añadir la verdura y el tomate rallado, sofocando bien.',
                    'Agregar el agua (o caldo) y el azafrán, dejar cocer 20 minutos.',
                    'Echar el arroz repartiéndolo por la paella y cocinar 18-20 minutos sin remover.'
                ],
                'duration' => 60,
                'difficulty' => 'media',
                'image_url' => 'https://images.unsplash.com/photo-1534080564607-c98bd8cc54a0?q=80&w=2070&auto=format&fit=crop',
                'categories' => ['Platos Principales', 'Arroz'],
                'ingredients' => [
                    ['name' => 'Arroz Bomba', 'quantity' => '400g', 'unit' => '-'],
                    ['name' => 'Pollo troceado', 'quantity' => '500g', 'unit' => '-'],
                    ['name' => 'Conejo', 'quantity' => '400g', 'unit' => '-'],
                    ['name' => 'Azafrán', 'quantity' => 'unas hebras', 'unit' => '-'],
                    ['name' => 'Aceite de Oliva Virgen Extra', 'quantity' => '100ml', 'unit' => '-']
                ]
            ],
            [
                'title' => 'Gazpacho Andaluz Tradicional',
                'description' => 'Refrescante sopa fría de hortalizas, ideal para los días de calor.',
                'instructions' => [
                    'Lavar y trocear los tomates, el pepino y los pimientos.',
                    'Triturar todo junto con el diente de ajo y el pan remojado.',
                    'Añadir el aceite, el vinagre y la sal mientras se sigue triturando.',
                    'Pasar por un colador fino y dejar enfriar en la nevera al menos 2 horas.'
                ],
                'duration' => 20,
                'difficulty' => 'facil',
                'image_url' => 'https://images.unsplash.com/photo-1594756202469-9ff9799b2e42?q=80&w=1976&auto=format&fit=crop',
                'categories' => ['Ligeros y Acompañantes', 'Sopas, caldos y cremas'],
                'ingredients' => [
                    ['name' => 'Tomate Maduro', 'quantity' => '1kg', 'unit' => '-'],
                    ['name' => 'Pepino', 'quantity' => '1 unidad', 'unit' => '-'],
                    ['name' => 'Pimiento Verde', 'quantity' => '1 unidad', 'unit' => '-'],
                    ['name' => 'Aceite de Oliva Virgen Extra', 'quantity' => '100ml', 'unit' => '-'],
                    ['name' => 'Vinagre de Jerez', 'quantity' => '30ml', 'unit' => '-']
                ]
            ],
            [
                'title' => 'Salmorejo Cordobés',
                'description' => 'Más espeso que el gazpacho y con un sabor intenso a tomate y buen aceite.',
                'instructions' => [
                    'Triturar los tomates y pasar por un colador para quitar pieles.',
                    'Añadir el pan y dejar que se empape bien con el tomate.',
                    'Añadir el ajo y el aceite de oliva, triturando a máxima potencia para emulsionar.',
                    'Servir muy frío decorado con huevo duro picado y jamón serrano.'
                ],
                'duration' => 15,
                'difficulty' => 'facil',
                'image_url' => 'https://images.unsplash.com/photo-1626078302251-163351ec896c?q=80&w=2070&auto=format&fit=crop',
                'categories' => ['Ligeros y Acompañantes', 'Sopas, caldos y cremas'],
                'ingredients' => [
                    ['name' => 'Tomate Maduro', 'quantity' => '1kg', 'unit' => '-'],
                    ['name' => 'Pan del día anterior', 'quantity' => '200g', 'unit' => '-'],
                    ['name' => 'Aceite de Oliva Virgen Extra', 'quantity' => '150ml', 'unit' => '-'],
                    ['name' => 'Jamón Serrano', 'quantity' => '50g', 'unit' => 'decoración'],
                    ['name' => 'Huevo', 'quantity' => '1 unidad', 'unit' => 'cocido']
                ]
            ],
            [
                'title' => 'Pulpo a la Gallega (Polbo á Feira)',
                'description' => 'La forma más tradicional de comer pulpo, tierno y con el toque picante del pimentón.',
                'instructions' => [
                    'Cocer el pulpo sumergiéndolo tres veces en agua hirviendo (asustarlo).',
                    'Cocinar durante 20-30 minutos hasta que esté tierno.',
                    'Cocer patatas en el mismo agua del pulpo.',
                    'Cortar el pulpo en rodajas sobre una base de patatas, aliñar con aceite, sal gruesa y pimentón.'
                ],
                'duration' => 45,
                'difficulty' => 'media',
                'image_url' => 'https://images.unsplash.com/photo-1599321955419-7853127bb9d2?q=80&w=2070&auto=format&fit=crop',
                'categories' => ['Entrantes y Snacks', 'Tapas y aperitivos'],
                'ingredients' => [
                    ['name' => 'Pulpo cocido', 'quantity' => '1kg', 'unit' => '-'],
                    ['name' => 'Patatas', 'quantity' => '500g', 'unit' => '-'],
                    ['name' => 'Pimentón dulce', 'quantity' => '1 cuch.', 'unit' => '-'],
                    ['name' => 'Pimentón picante', 'quantity' => 'al gusto', 'unit' => '-'],
                    ['name' => 'Aceite de Oliva Virgen Extra', 'quantity' => '50ml', 'unit' => '-']
                ]
            ],
            [
                'title' => 'Tortilla de Patatas con Cebolla',
                'description' => 'El debate nacional: con cebolla o sin ella. Nosotros la preferimos jugosa y con cebolla.',
                'instructions' => [
                    'Cortar las patatas y la cebolla en láminas finas.',
                    'Freír a fuego lento en abundante aceite hasta que estén tiernas (confitadas).',
                    'Escurrir el aceite y mezclar con los huevos batidos. Reposar 5 minutos.',
                    'Cuajar en la sartén por ambos lados con un poco de aceite a fuego medio.'
                ],
                'duration' => 40,
                'difficulty' => 'media',
                'image_url' => 'https://images.unsplash.com/photo-1633383718081-22ac93e3dbf1?q=80&w=2070&auto=format&fit=crop',
                'categories' => ['Entrantes y Snacks', 'Huevos y tortillas'],
                'ingredients' => [
                    ['name' => 'Patatas', 'quantity' => '800g', 'unit' => '-'],
                    ['name' => 'Huevo', 'quantity' => '6 unidades', 'unit' => '-'],
                    ['name' => 'Cebolla', 'quantity' => '1 unidad', 'unit' => '-'],
                    ['name' => 'Aceite de Oliva Virgen Extra', 'quantity' => 'al gusto', 'unit' => '-']
                ]
            ],
            [
                'title' => 'Fabada Asturiana Auténtica',
                'description' => 'El guiso más famoso de Asturias, contundente y lleno de sabor.',
                'instructions' => [
                    'Poner las alubias (fabes) a remojo la noche anterior.',
                    'Cocer las alubias con el chorizo, morcilla, lacón y panceta a fuego lento.',
                    'Espumar durante la cocción y "asustar" con agua fría un par de veces.',
                    'Cocinar unas 2-3 horas hasta que las alubias estén mantecosas.'
                ],
                'duration' => 180,
                'difficulty' => 'media',
                'image_url' => 'https://images.unsplash.com/photo-1547928576-a4a33237bec3?q=80&w=2070&auto=format&fit=crop',
                'categories' => ['Platos Principales', 'Legumbres y guisos'],
                'ingredients' => [
                    ['name' => 'Fabulosas (Alubias blancas)', 'quantity' => '500g', 'unit' => '-'],
                    ['name' => 'Chorizo Asturiano', 'quantity' => '2 piezas', 'unit' => '-'],
                    ['name' => 'Morcilla de Burgos', 'quantity' => '2 piezas', 'unit' => '-'],
                    ['name' => 'Lacón', 'quantity' => '200g', 'unit' => '-'],
                    ['name' => 'Panceta', 'quantity' => '150g', 'unit' => '-']
                ]
            ],
            [
                'title' => 'Croquetas de Jamón Ibérico',
                'description' => 'Crujientes por fuera y cremosas por dentro. El secreto de toda madre.',
                'instructions' => [
                    'Hacer una bechamel sofriendo harina con mantequilla y añadiendo leche poco a poco.',
                    'Añadir el jamón picado y cocinar hasta que la masa se despegue de la sartén.',
                    'Dejar enfriar la masa en una fuente tapada con film.',
                    'Formar las croquetas, pasar por huevo y pan rallado y freír en aceite muy caliente.'
                ],
                'duration' => 60,
                'difficulty' => 'dificil',
                'image_url' => 'https://images.unsplash.com/photo-1562967914-608f82629710?q=80&w=2073&auto=format&fit=crop',
                'categories' => ['Entrantes y Snacks', 'Tapas y aperitivos'],
                'ingredients' => [
                    ['name' => 'Jamón Serrano', 'quantity' => '150g', 'unit' => '-'],
                    ['name' => 'Harina de Trigo', 'quantity' => '100g', 'unit' => '-'],
                    ['name' => 'Leche entera', 'quantity' => '1 litro', 'unit' => '-'],
                    ['name' => 'Mantequilla', 'quantity' => '80g', 'unit' => '-'],
                    ['name' => 'Huevo', 'quantity' => '2 unidades', 'unit' => '-']
                ]
            ],
            [
                'title' => 'Gambas al Ajillo',
                'description' => 'Un clásico de las tapas españolas. Rápido, sencillo y delicioso.',
                'instructions' => [
                    'Pelar las gambas y laminar los ajos.',
                    'En una cazuela de barro, calentar abundante aceite con los ajos y una guindilla.',
                    'Cuando los ajos empiecen a dorarse, añadir las gambas.',
                    'Cocinar 1-2 minutos, salpimentar y servir burbujeando con perejil fresco.'
                ],
                'duration' => 10,
                'difficulty' => 'facil',
                'image_url' => 'https://images.unsplash.com/photo-1533759413974-9e15f3b745ac?q=80&w=2070&auto=format&fit=crop',
                'categories' => ['Entrantes y Snacks', 'Tapas y aperitivos'],
                'ingredients' => [
                    ['name' => 'Gambas frescas', 'quantity' => '400g', 'unit' => '-'],
                    ['name' => 'Ajo', 'quantity' => '4 dientes', 'unit' => '-'],
                    ['name' => 'Aceite de Oliva Virgen Extra', 'quantity' => '100ml', 'unit' => '-'],
                    ['name' => 'Sal', 'quantity' => 'al gusto', 'unit' => '-'],
                    ['name' => 'Perejil fresco', 'quantity' => 'al gusto', 'unit' => '-']
                ]
            ],
            [
                'title' => 'Lentejas con Chorizo',
                'description' => 'Plato de cuchara tradicional que siempre reconforta.',
                'instructions' => [
                    'Hacer un sofrito con cebolla, ajo y pimiento rojo.',
                    'Añadir las lentejas (previamente lavadas), el chorizo en rodajas y la patata troceada.',
                    'Cubrir con agua, añadir el laurel y el pimentón dulce.',
                    'Cocer a fuego medio-bajo durante 40-50 minutos hasta que estén tiernas.'
                ],
                'duration' => 50,
                'difficulty' => 'facil',
                'image_url' => 'https://images.unsplash.com/photo-1547592166-23ac45744a05?q=80&w=2071&auto=format&fit=crop',
                'categories' => ['Platos Principales', 'Legumbres y guisos'],
                'ingredients' => [
                    ['name' => 'Garbanzos', 'quantity' => '300g', 'unit' => '(usar lentejas en descripción)'],
                    ['name' => 'Chorizo Asturiano', 'quantity' => '1 pieza', 'unit' => '-'],
                    ['name' => 'Patatas', 'quantity' => '2 unidades', 'unit' => '-'],
                    ['name' => 'Cebolla', 'quantity' => '1 unidad', 'unit' => '-'],
                    ['name' => 'Pimentón dulce', 'quantity' => '1 cuch.', 'unit' => '-']
                ]
            ],
            [
                'title' => 'Tarta de Santiago',
                'description' => 'Tradicional postre gallego a base de almendras, sin gluten y delicioso.',
                'instructions' => [
                    'Mezclar las almendras molidas con el azúcar.',
                    'Añadir los huevos uno a uno mezclando suavemente (sin batir en exceso).',
                    'Añadir ralladura de limón y canela.',
                    'Hornear a 180°C durante 30 minutos. Decorar con la cruz de Santiago en azúcar glass.'
                ],
                'duration' => 45,
                'difficulty' => 'facil',
                'image_url' => 'https://images.unsplash.com/photo-1508737804141-4c3b688e2546?q=80&w=2070&auto=format&fit=crop',
                'categories' => ['Dulces y Bebidas', 'Postres y dulces'],
                'ingredients' => [
                    ['name' => 'Almendras', 'quantity' => '250g', 'unit' => 'molidas'],
                    ['name' => 'Azúcar', 'quantity' => '250g', 'unit' => '-'],
                    ['name' => 'Huevo', 'quantity' => '5 unidades', 'unit' => '-'],
                    ['name' => 'Canela en rama', 'quantity' => '1 cuch. molida', 'unit' => '-'],
                    ['name' => 'Cáscara de limón', 'quantity' => 'ralladura', 'unit' => '-']
                ]
            ],
            [
                'title' => 'Churros con Chocolate',
                'description' => 'El desayuno o merienda por excelencia en España.',
                'instructions' => [
                    'Hervir agua con una pizca de sal y añadir la harina de golpe, mezclando hasta formar una masa.',
                    'Introducir la masa en una churrera o manga pastelera con boquilla de estrella.',
                    'Freír en abundante aceite muy caliente hasta que estén dorados.',
                    'Servir con chocolate a la taza bien espeso.'
                ],
                'duration' => 30,
                'difficulty' => 'media',
                'image_url' => 'https://images.unsplash.com/photo-1558231011-37d457632616?q=80&w=2070&auto=format&fit=crop',
                'categories' => ['Dulces y Bebidas', 'Postres y dulces'],
                'ingredients' => [
                    ['name' => 'Harina de Trigo', 'quantity' => '250g', 'unit' => '-'],
                    ['name' => 'Aceite de Oliva Virgen Extra', 'quantity' => 'para freír', 'unit' => '-'],
                    ['name' => 'Chocolate para fundir', 'quantity' => '200g', 'unit' => '-'],
                    ['name' => 'Leche entera', 'quantity' => '500ml', 'unit' => 'para chocolate'],
                    ['name' => 'Sal', 'quantity' => 'una pizca', 'unit' => '-']
                ]
            ],
            [
                'title' => 'Bacalao al Pil-Pil',
                'description' => 'Una maravilla de la cocina vasca: salsa ligada con la gelatina del bacalao.',
                'instructions' => [
                    'Laminar muchos ajos y freírlos en aceite hasta que doren, luego retirar.',
                    'Refreír el bacalao a fuego muy suave con la piel hacia arriba.',
                    'Retirar el bacalao y dejar templar el aceite un poco.',
                    'Mover la cazuela rítmicamente añadiendo gotas de agua o suero para ligar la salsa.'
                ],
                'duration' => 50,
                'difficulty' => 'dificil',
                'image_url' => 'https://images.unsplash.com/photo-1534947522204-6b8017c69997?q=80&w=2070&auto=format&fit=crop',
                'categories' => ['Platos Principales', 'Pescado y marisco'],
                'ingredients' => [
                    ['name' => 'Bacalao desalado', 'quantity' => '4 lomos', 'unit' => '-'],
                    ['name' => 'Aceite de Oliva Virgen Extra', 'quantity' => '250ml', 'unit' => '-'],
                    ['name' => 'Ajo', 'quantity' => '6 dientes', 'unit' => '-']
                ]
            ],
            [
                'title' => 'Arroz con Leche Cremoso',
                'description' => 'Postre tradicional, muy suave y con el toque de canela quemada arriba.',
                'instructions' => [
                    'Lavar el arroz y cocerlo con agua unos 10 minutos.',
                    'Añadir la leche caliente, la canela y la cáscara de limón.',
                    'Cocinar a fuego muy lento removiendo casi constantemente durante 45 minutos.',
                    'Añadir el azúcar al final, cocinar 5 minutos más y servir con canela en polvo.'
                ],
                'duration' => 60,
                'difficulty' => 'media',
                'image_url' => 'https://images.unsplash.com/photo-1626078302251-163351ec896c?q=80&w=2070&auto=format&fit=crop',
                'categories' => ['Dulces y Bebidas', 'Postres y dulces'],
                'ingredients' => [
                    ['name' => 'Arroz Bomba', 'quantity' => '200g', 'unit' => '-'],
                    ['name' => 'Leche entera', 'quantity' => '1.5 litros', 'unit' => '-'],
                    ['name' => 'Azúcar', 'quantity' => '150g', 'unit' => '-'],
                    ['name' => 'Canela en rama', 'quantity' => '1 unidad', 'unit' => '-'],
                    ['name' => 'Cáscara de limón', 'quantity' => '1 unidad', 'unit' => '-']
                ]
            ],
            [
                'title' => 'Patatas Bravas Picantes',
                'description' => 'La tapa reina de los bares españoles. Crujientes y con salsa brava auténtica.',
                'instructions' => [
                    'Cortar las patatas en dados irregulares grandes.',
                    'Freír a fuego lento primero (pocaharlas) y luego a fuego fuerte hasta que doren.',
                    'Para la salsa: sofreír cebolla con pimentón picante y harina, añadir caldo de pollo y triturar.',
                    'Servir las patatas calientes bañadas en la salsa brava.'
                ],
                'duration' => 35,
                'difficulty' => 'media',
                'image_url' => 'https://images.unsplash.com/photo-1582234372722-50d7ccc30e5a?q=80&w=2070&auto=format&fit=crop',
                'categories' => ['Entrantes y Snacks', 'Tapas y aperitivos'],
                'ingredients' => [
                    ['name' => 'Patatas', 'quantity' => '1kg', 'unit' => '-'],
                    ['name' => 'Pimentón picante', 'quantity' => '2 cuch.', 'unit' => '-'],
                    ['name' => 'Harina de Trigo', 'quantity' => '1 cuch.', 'unit' => '-'],
                    ['name' => 'Caldo de pollo', 'quantity' => '250ml', 'unit' => '-'],
                    ['name' => 'Aceite de Oliva Virgen Extra', 'quantity' => 'para freír', 'unit' => '-']
                ]
            ],
            [
                'title' => 'Cochinillo Asado al Estilo Segoviano',
                'description' => 'Carne tierna que se deshace y piel crujiente. Tradición pura de Castilla.',
                'instructions' => [
                    'Abrir el cochinillo por la mitad y salar.',
                    'Colocar en una bandeja de barro con un poco de agua en el fondo (sin tocar la piel).',
                    'Asar a 180°C durante unas 2.5 - 3 horas.',
                    'Pincelar con manteca o aceite de vez en cuando y subir el fuego al final para que la piel cruja.'
                ],
                'duration' => 180,
                'difficulty' => 'dificil',
                'image_url' => 'https://images.unsplash.com/photo-1568289463255-08e063864796?q=80&w=2070&auto=format&fit=crop',
                'categories' => ['Platos Principales', 'Carnes y aves'],
                'ingredients' => [
                    ['name' => 'Cochinillo', 'quantity' => '1 unidad (4kg)', 'unit' => '-'],
                    ['name' => 'Sal', 'quantity' => 'al gusto', 'unit' => '-'],
                    ['name' => 'Aceite de Oliva Virgen Extra', 'quantity' => '50ml', 'unit' => '-'],
                    ['name' => 'Laurel', 'quantity' => '2 hojas', 'unit' => '-']
                ]
            ],
            [
                'title' => 'Ensalada Mixta de Verano',
                'description' => 'El acompañamiento perfecto para cualquier plato de pescado o carne.',
                'instructions' => [
                    'Lavar bien la lechuga y el tomate, cortarlos a gusto.',
                    'Cocer el huevo, pelarlo y cortarlo en cuartos.',
                    'Añadir cebolla en juliana, aceitunas y atún (usar pescado en seeder).',
                    'Aliñar con sal, vinagre y un generoso chorro de aceite de oliva virgen extra.'
                ],
                'duration' => 10,
                'difficulty' => 'facil',
                'image_url' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?q=80&w=2070&auto=format&fit=crop',
                'categories' => ['Ligeros y Acompañantes', 'Ensaladas'],
                'ingredients' => [
                    ['name' => 'Tomate Maduro', 'quantity' => '2 unidades', 'unit' => '-'],
                    ['name' => 'Huevo', 'quantity' => '2 unidades', 'unit' => 'cocido'],
                    ['name' => 'Cebolla', 'quantity' => '0.5 unidades', 'unit' => '-'],
                    ['name' => 'Aceite de Oliva Virgen Extra', 'quantity' => 'al gusto', 'unit' => '-'],
                    ['name' => 'Vinagre de Jerez', 'quantity' => 'al gusto', 'unit' => '-']
                ]
            ],
            [
                'title' => 'Torrijas de Leche de Semana Santa',
                'description' => 'El dulce más típico de la Pascua española, hecho con pan, leche y amor.',
                'instructions' => [
                    'Infundir la leche con canela y cáscara de limón y naranja. Añadir azúcar.',
                    'Empapar rebanadas de pan grueso en la leche hasta que estén bien jugosas.',
                    'Pasar por huevo batido y freír en abundante aceite caliente.',
                    'Rebozar en una mezcla de azúcar y canela en polvo mientras estén calientes.'
                ],
                'duration' => 45,
                'difficulty' => 'media',
                'image_url' => 'https://images.unsplash.com/photo-1504113888839-1c8ec7ca7721?q=80&w=2070&auto=format&fit=crop',
                'categories' => ['Dulces y Bebidas', 'Postres y dulces'],
                'ingredients' => [
                    ['name' => 'Pan del día anterior', 'quantity' => '1 barra', 'unit' => '-'],
                    ['name' => 'Leche entera', 'quantity' => '1 litro', 'unit' => '-'],
                    ['name' => 'Huevo', 'quantity' => '3 unidades', 'unit' => '-'],
                    ['name' => 'Azúcar', 'quantity' => '200g', 'unit' => '-'],
                    ['name' => 'Canela en rama', 'quantity' => '1 unidad', 'unit' => '-']
                ]
            ],
            [
                'title' => 'Cordero Lechal Asado',
                'description' => 'Un manjar típico de Aranda de Duero, asado lentamente con agua y sal.',
                'instructions' => [
                    'Colocar el cordero en una fuente de barro con la piel hacia abajo.',
                    'Añadir un vaso de agua al fondo y salar bien.',
                    'Asar a 160°C durante 1.5 horas, dándole la vuelta a mitad del proceso.',
                    'Subir el horno al final para que la piel quede bien dorada y crujiente.'
                ],
                'duration' => 120,
                'difficulty' => 'media',
                'image_url' => 'https://images.unsplash.com/photo-1544025162-d76694265947?q=80&w=2069&auto=format&fit=crop',
                'categories' => ['Platos Principales', 'Carnes y aves'],
                'ingredients' => [
                    ['name' => 'Cordero lechal', 'quantity' => '2kg', 'unit' => '-'],
                    ['name' => 'Sal', 'quantity' => 'al gusto', 'unit' => '-'],
                    ['name' => 'Vino Blanco', 'quantity' => '50ml', 'unit' => 'opcional'],
                    ['name' => 'Aceite de Oliva Virgen Extra', 'quantity' => 'un chorrito', 'unit' => '-']
                ]
            ],
            [
                'title' => 'Calamares a la Romana Crujientes',
                'description' => 'El secreto está en el rebozado ligero y los calamares frescos.',
                'instructions' => [
                    'Limpiar los calamares y cortarlos en anillas.',
                    'Pasar las anillas por harina, sacudiendo el exceso.',
                    'Pasar por huevo batido con un poco de agua o cerveza.',
                    'Freír en abundante aceite muy caliente hasta que estén rubios.'
                ],
                'duration' => 25,
                'difficulty' => 'facil',
                'image_url' => 'https://images.unsplash.com/photo-1599487488170-d11ec9c172f0?q=80&w=2070&auto=format&fit=crop',
                'categories' => ['Entrantes y Snacks', 'Tapas y aperitivos'],
                'ingredients' => [
                    ['name' => 'Calamares', 'quantity' => '500g', 'unit' => '-'],
                    ['name' => 'Harina de Trigo', 'quantity' => '100g', 'unit' => '-'],
                    ['name' => 'Huevo', 'quantity' => '2 unidades', 'unit' => '-'],
                    ['name' => 'Aceite de Oliva Virgen Extra', 'quantity' => 'para freír', 'unit' => '-'],
                    ['name' => 'Cáscara de limón', 'quantity' => 'para servir', 'unit' => '-']
                ]
            ],
            [
                'title' => 'Pimientos del Padrón (Unos pican y otros no)',
                'description' => 'Plato sencillísimo que siempre es una aventura comer.',
                'instructions' => [
                    'Lavar y secar muy bien los pimientos.',
                    'Freír en abundante aceite de oliva caliente hasta que estén tiernos y la piel se hinche un poco.',
                    'Escurrir sobre papel absorbente.',
                    'Espolvorear con abundante sal gruesa y servir inmediatamente.'
                ],
                'duration' => 10,
                'difficulty' => 'facil',
                'image_url' => 'https://images.unsplash.com/photo-1608634812320-136fec83bc39?q=80&w=1974&auto=format&fit=crop',
                'categories' => ['Entrantes y Snacks', 'Tapas y aperitivos'],
                'ingredients' => [
                    ['name' => 'Pimiento Verde', 'quantity' => '500g', 'unit' => 'de Padrón'],
                    ['name' => 'Aceite de Oliva Virgen Extra', 'quantity' => '100ml', 'unit' => '-'],
                    ['name' => 'Sal', 'quantity' => 'gruesa al gusto', 'unit' => '-']
                ]
            ]
        ];

        foreach ($recipesData as $data) {
            $recipe = Recipe::updateOrCreate(
                ['title' => $data['title']],
                [
                    'user_id' => $user->id,
                    'description' => $data['description'],
                    'instructions' => json_encode($data['instructions']),
                    'duration' => $data['duration'],
                    'difficulty' => $data['difficulty'],
                    'status' => 'published',
                    'image_url' => $data['image_url']
                ]
            );

            // Categorías
            $catIds = Category::whereIn('name', $data['categories'])->pluck('id');
            $recipe->categories()->sync($catIds);

            // Ingredientes
            $ingredientSyncData = [];
            foreach ($data['ingredients'] as $ingData) {
                $ingredient = Ingredient::where('name', $ingData['name'])->first();
                if ($ingredient) {
                    $ingredientSyncData[$ingredient->id] = [
                        'quantity' => $ingData['quantity'],
                        'unit' => $ingData['unit']
                    ];
                }
            }
            $recipe->ingredients()->sync($ingredientSyncData);
        }
    }
}
