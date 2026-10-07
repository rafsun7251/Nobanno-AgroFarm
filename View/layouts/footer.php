<footer class="footer">

    <div class="footer-container">

        <div class="footer-brand">

            <h3>
                নবান্ন
            </h3>

            <p>
                Fresh products directly
                from local farmers.
            </p>

        </div>


        <div class="footer-links">

            <h4>
                Quick Links
            </h4>

            <a href="index.php">
                Home
            </a>

            <a href="index.php?page=crops">
                Crops
            </a>

        </div>


        <div class="footer-contact">

            <h4>
                Contact
            </h4>

            <p>
                Dhaka, Bangladesh
            </p>

            <p>
                info@nobannoagro.com
            </p>

        </div>

    </div>


    <div class="footer-bottom">

        <p>
            &copy;
            <?= date('Y') ?>
            Nobanno Agro Farm.
            All rights reserved.
        </p>

    </div>

</footer>


<script src="public/js/main.js"></script>

<?php if (!empty($extraJs)): ?>

    <?php foreach ($extraJs as $js): ?>

        <script
            src="<?= htmlspecialchars($js) ?>"
        ></script>

    <?php endforeach; ?>

<?php endif; ?>

</body>

</html>