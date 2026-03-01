<?php
/**
 * VMaNGOS Registration Portal - Footer Template
 **/
?>
<div class="section-divider"></div>
<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-left">Emberstone &middot; For the Horde &middot; For the Alliance</div>
        <div class="footer-right">
            <?php echo (new SebastianBergmann\Timer\ResourceUsageFormatter)->resourceUsageSinceStartOfRequest(); ?>
        </div>
    </div>
</footer>
</div><!-- /.container -->
</div><!-- /.content1 -->

<script>
/* ── Custom navbar ─────────────────────────────────── */
(function() {
    var nav = document.getElementById('site-nav');
    var toggle = document.getElementById('nav-toggle');
    var mobile = document.getElementById('nav-mobile');

    window.addEventListener('scroll', function() {
        nav.classList.toggle('scrolled', window.scrollY > 40);
    }, {passive: true});

    toggle.addEventListener('click', function() {
        var open = mobile.classList.toggle('open');
        toggle.classList.toggle('open', open);
    });

    window.closeMobileNav = function() {
        mobile.classList.remove('open');
        toggle.classList.remove('open');
    };
})();

/* ── Smooth scroll to content section ─────────────── */
window.scrollToContent = function() {
    setTimeout(function() {
        var el = document.getElementById('nav-tabContent');
        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, 150);
};

/* ── Arcane particle canvas ────────────────────────── */
(function() {
    var c = document.getElementById('arcane-canvas');
    if (!c) return;
    var ctx = c.getContext('2d');
    var w, h, particles = [], runeAngle = 0;

    function resize() {
        w = c.width = window.innerWidth;
        h = c.height = window.innerHeight;
    }
    resize();
    window.addEventListener('resize', resize);

    // Particle class
    function Particle() {
        this.reset();
    }
    Particle.prototype.reset = function() {
        this.x = Math.random() * w;
        this.y = h + Math.random() * 100;
        this.size = Math.random() * 2.5 + 0.5;
        this.speedY = -(Math.random() * 0.6 + 0.15);
        this.speedX = (Math.random() - 0.5) * 0.3;
        this.opacity = Math.random() * 0.5 + 0.1;
        this.life = 0;
        this.maxLife = Math.random() * 400 + 200;
    };
    Particle.prototype.update = function() {
        this.x += this.speedX + Math.sin(this.life * 0.008) * 0.15;
        this.y += this.speedY;
        this.life++;
        var progress = this.life / this.maxLife;
        this.currentOpacity = this.opacity * (progress < 0.1 ? progress / 0.1 : progress > 0.8 ? (1 - progress) / 0.2 : 1);
        if (this.life >= this.maxLife || this.y < -20) this.reset();
    };
    Particle.prototype.draw = function() {
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        ctx.fillStyle = 'rgba(201, 168, 76, ' + this.currentOpacity + ')';
        ctx.fill();
        if (this.size > 1.5) {
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.size * 3, 0, Math.PI * 2);
            ctx.fillStyle = 'rgba(201, 168, 76, ' + (this.currentOpacity * 0.12) + ')';
            ctx.fill();
        }
    };

    for (var i = 0; i < 80; i++) {
        var p = new Particle();
        p.life = Math.random() * p.maxLife;
        p.y = Math.random() * h;
        particles.push(p);
    }

    // Rune circle
    function drawRune(cx, cy, radius, angle) {
        ctx.save();
        ctx.translate(cx, cy);
        ctx.rotate(angle);

        // Outer ring
        ctx.beginPath();
        ctx.arc(0, 0, radius, 0, Math.PI * 2);
        ctx.strokeStyle = 'rgba(201, 168, 76, 0.06)';
        ctx.lineWidth = 1.5;
        ctx.stroke();

        // Inner ring
        ctx.beginPath();
        ctx.arc(0, 0, radius * 0.72, 0, Math.PI * 2);
        ctx.strokeStyle = 'rgba(201, 168, 76, 0.04)';
        ctx.lineWidth = 1;
        ctx.stroke();

        // Rune marks
        for (var i = 0; i < 8; i++) {
            var a = (Math.PI * 2 / 8) * i;
            var x1 = Math.cos(a) * radius * 0.72;
            var y1 = Math.sin(a) * radius * 0.72;
            var x2 = Math.cos(a) * radius;
            var y2 = Math.sin(a) * radius;
            ctx.beginPath();
            ctx.moveTo(x1, y1);
            ctx.lineTo(x2, y2);
            ctx.strokeStyle = 'rgba(201, 168, 76, 0.05)';
            ctx.lineWidth = 1;
            ctx.stroke();
        }

        // Diamond markers
        for (var j = 0; j < 4; j++) {
            var a2 = (Math.PI * 2 / 4) * j + Math.PI / 4;
            var dx = Math.cos(a2) * radius * 0.86;
            var dy = Math.sin(a2) * radius * 0.86;
            ctx.save();
            ctx.translate(dx, dy);
            ctx.rotate(a2);
            ctx.beginPath();
            ctx.moveTo(0, -4);
            ctx.lineTo(3, 0);
            ctx.lineTo(0, 4);
            ctx.lineTo(-3, 0);
            ctx.closePath();
            ctx.fillStyle = 'rgba(201, 168, 76, 0.08)';
            ctx.fill();
            ctx.restore();
        }

        ctx.restore();
    }

    // Animate
    function animate() {
        requestAnimationFrame(animate);
        ctx.clearRect(0, 0, w, h);

        // Draw rune circle centered in top half
        runeAngle += 0.001;
        drawRune(w / 2, h * 0.38, Math.min(w, h) * 0.28, runeAngle);
        drawRune(w / 2, h * 0.38, Math.min(w, h) * 0.20, -runeAngle * 1.4);

        // Draw particles
        for (var i = 0; i < particles.length; i++) {
            particles[i].update();
            particles[i].draw();
        }
    }
    animate();
})();
</script>
</body>
</html>
