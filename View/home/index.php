<?php

$pageTitle = 'Home';

require __DIR__ . '/../layouts/header.php';

?>

<main class="home-page">


    <!-- Hero Section -->

    <section class="hero">

        <div class="hero-content">

            <span class="hero-label">
                Fresh • Local • Trusted
            </span>


            <h1>
                Fresh Products
                Directly From Farmers
            </h1>


            <p>
                Buy fresh agricultural products
                directly from local farmers
                through Nobanno Agro Farm.
            </p>


            <div class="hero-actions">

                <a
                    href="index.php?page=crops"
                    class="btn btn-primary"
                >
                    Shop Now
                </a>


                <a
                    href="index.php?page=become-farmer"
                    class="btn btn-secondary"
                >
                    Become a Farmer
                </a>

            </div>

        </div>

    </section>


    <!-- Features -->

    <section class="features">

        <div class="page-container">

            <div class="section-header">

                <h2>
                    Why Nobanno Agro Farm?
                </h2>

                <p>
                    Connecting farmers
                    directly with customers.
                </p>

            </div>


            <div class="feature-grid">


                <div class="feature-card">

                    <h3>
                        Fresh Products
                    </h3>

                    <p>
                        Get fresh agricultural
                        products from local farms.
                    </p>

                </div>


                <div class="feature-card">

                    <h3>
                        Direct From Farmers
                    </h3>

                    <p>
                        Farmers can sell their
                        products directly to customers.
                    </p>

                </div>


                <div class="feature-card">

                    <h3>
                        Easy Ordering
                    </h3>

                    <p>
                        Browse products, add to cart
                        and place your order easily.
                    </p>

                </div>


                <div class="feature-card">

                    <h3>
                        Reliable Delivery
                    </h3>

                    <p>
                        Our delivery system helps
                        bring products to your doorstep.
                    </p>

                </div>


            </div>

        </div>

    </section>


    <!-- Call To Action -->

    <section class="home-cta">

        <div class="page-container">

            <h2>
                Ready to buy fresh products?
            </h2>

            <p>
                Explore our available farm products.
            </p>


            <a
                href="index.php?page=crops"
                class="btn btn-primary"
            >
                Explore Crops
            </a>

        </div>

    </section>


</main>


<?php

require __DIR__
    . '/../layouts/footer.php';

?>