<?php if(Matomo::allowed()) : ?>

    <div class="matomo-opt-out">
        <p class="matomo-opt-out__description"><?= t('matomo.optout.description') ?></p>
        <p class="matomo-opt-out__instructions"><?= t('matomo.optout.instructions') ?></p>
        <p class="matomo-opt-out__toggle">
            <input type="checkbox" id="matomo-optout" />
            <label for="matomo-optout"></label>
        </p>
    </div>
    <script>
    document.addEventListener("DOMContentLoaded", function(event) {
        var _paq = window._paq || [];
        function setOptOutText(element) {
            _paq.push([function() {
            element.checked = !this.isUserOptedOut();
            document.querySelector('label[for=matomo-optout]').innerText = this.isUserOptedOut()
                ? '<?= t('matomo.optout.optin') ?>'
                : '<?= t('matomo.optout.optout') ?>';
            }]);
        }
        var optOut = document.getElementById("matomo-optout");
        optOut.addEventListener("click", function() {
            if (this.checked) {
            _paq.push(['forgetUserOptOut']);
            } else {
            _paq.push(['optUserOut']);
            }
            setOptOutText(optOut);
        });
        setOptOutText(optOut);
    });
    </script>

<?php else : ?>

<!-- Kirby Matomo opt-out -->

<?php endif; ?>
