/*-----------------------------------------------------------------------------------
    Da Andrea — Pizzeria & Enoteca
    Homepage interactions (imported from Claude Design: "Da Andrea Light")
    Vanilla JS, no dependencies.
-----------------------------------------------------------------------------------*/
(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {
		initGlassNav();
		initBurger();
		initHashScroll();
		initReveal();
		initTabs();
		initForm();
		initEmbers();
	});

	/* --------------- Smooth scroll for nav hash links --------------- */
	function initHashScroll() {
		var links = document.querySelectorAll('a[data-hash][data-href]');
		links.forEach(function (link) {
			link.addEventListener('click', function (e) {
				var target = document.querySelector(link.getAttribute('data-href'));
				if (!target) return;
				e.preventDefault();
				var offset = parseInt(link.getAttribute('data-hash-offset'), 10) || 0;
				var top = target.getBoundingClientRect().top + window.pageYOffset - offset;
				window.scrollTo({ top: top, behavior: 'smooth' });
			});
		});
	}

	/* --------------- Glass nav on scroll --------------- */
	function initGlassNav() {
		var nav = document.querySelector('.da-nav');
		if (!nav) return;
		var onScroll = function () {
			nav.classList.toggle('scrolled', window.scrollY > 40);
		};
		window.addEventListener('scroll', onScroll, { passive: true });
		onScroll();
	}

	/* --------------- Mobile drawer --------------- */
	function initBurger() {
		var burger = document.querySelector('.da-burger');
		var drawer = document.querySelector('.da-drawer');
		if (!burger || !drawer) return;
		burger.addEventListener('click', function () {
			drawer.classList.toggle('open');
		});
		drawer.querySelectorAll('a').forEach(function (a) {
			a.addEventListener('click', function () { drawer.classList.remove('open'); });
		});
	}

	/* --------------- Reveal on scroll --------------- */
	function initReveal() {
		var els = document.querySelectorAll('[data-reveal]');
		if (!els.length) return;

		if (!('IntersectionObserver' in window)) {
			els.forEach(function (el) { el.style.opacity = '1'; el.style.transform = 'none'; });
			return;
		}
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (e) {
				if (e.isIntersecting) {
					e.target.style.opacity = '1';
					e.target.style.transform = 'none';
					io.unobserve(e.target);
				}
			});
		}, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });

		els.forEach(function (el, i) {
			el.style.transitionDelay = ((i % 3) * 0.08) + 's';
			io.observe(el);
		});
	}

	/* --------------- Menu tabs --------------- */
	function initTabs() {
		var tabs = document.querySelectorAll('.da-tab');
		var panels = document.querySelectorAll('.da-menu-panel');
		if (!tabs.length) return;

		tabs.forEach(function (tab) {
			tab.addEventListener('click', function () {
				var target = tab.getAttribute('data-tab');
				tabs.forEach(function (t) { t.classList.toggle('active', t === tab); });
				panels.forEach(function (p) {
					p.classList.toggle('active', p.getAttribute('data-panel') === target);
				});
			});
		});
	}

	/* --------------- Reservation form --------------- */
	function initForm() {
		var form = document.querySelector('.da-form');
		var sent = document.querySelector('.da-sent');
		if (!form || !sent) return;

		var nameInput = form.querySelector('[name="nome"]');
		var submitBtn = form.querySelector('.da-submit');
		form.addEventListener('submit', function (e) {
			e.preventDefault();
			if (submitBtn) submitBtn.disabled = true;

			fetch(form.action, {
				method: 'POST',
				body: new FormData(form),
				headers: { 'X-Requested-With': 'XMLHttpRequest' }
			}).then(function (res) {
				return res.json().catch(function () { return { ok: res.ok }; });
			}).then(function (data) {
				if (!data || !data.ok) throw new Error((data && data.message) || 'Invio non riuscito.');
				var first = nameInput && nameInput.value ? nameInput.value.split(' ')[0] : '';
				var nameSlot = sent.querySelector('.da-sent-name');
				if (nameSlot) nameSlot.textContent = first ? (' ' + first) : '';
				form.style.display = 'none';
				sent.classList.add('show');
			}).catch(function (err) {
				if (submitBtn) submitBtn.disabled = false;
				alert(err.message || 'Invio non riuscito. Riprova o chiamaci direttamente.');
			});
		});
	}

	/* --------------- Hero ember particles --------------- */
	function initEmbers() {
		var canvas = document.getElementById('ember-canvas');
		if (!canvas || !canvas.getContext) return;

		var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		if (reduce) return;

		var ctx = canvas.getContext('2d');
		var dpr = window.devicePixelRatio || 1;
		var w, h, parts = [], raf;
		var N = 70;
		var rnd = function (a, b) { return a + Math.random() * (b - a); };

		function resize() {
			var r = canvas.parentElement.getBoundingClientRect();
			w = canvas.width = r.width * dpr;
			h = canvas.height = r.height * dpr;
		}

		function spawn() {
			return {
				x: rnd(0, w), y: rnd(h * 0.4, h + 40),
				r: rnd(0.6, 2.4) * dpr,
				vy: rnd(0.25, 1.1) * dpr,
				vx: rnd(-0.35, 0.35) * dpr,
				a: rnd(0.15, 0.85),
				hue: Math.random() < 0.35 ? 30 : 16,
				flick: rnd(0.005, 0.03)
			};
		}

		function tick() {
			ctx.clearRect(0, 0, w, h);
			for (var i = 0; i < parts.length; i++) {
				var p = parts[i];
				p.y -= p.vy; p.x += p.vx; p.a -= p.flick * 0.3;
				p.vx += rnd(-0.02, 0.02) * dpr;
				if (p.y < -20 || p.a <= 0) { parts[i] = spawn(); parts[i].y = h + 20; continue; }
				var g = ctx.createRadialGradient(p.x, p.y, 0, p.x, p.y, p.r * 3.5);
				g.addColorStop(0, 'hsla(' + p.hue + ',95%,60%,' + Math.max(0, p.a) + ')');
				g.addColorStop(1, 'hsla(20,95%,55%,0)');
				ctx.fillStyle = g;
				ctx.beginPath(); ctx.arc(p.x, p.y, p.r * 3.5, 0, 7); ctx.fill();
			}
			raf = requestAnimationFrame(tick);
		}

		resize();
		window.addEventListener('resize', resize);
		for (var k = 0; k < N; k++) parts.push(spawn());
		tick();
	}

})();
