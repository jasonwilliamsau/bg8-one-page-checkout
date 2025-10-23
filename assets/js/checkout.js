document.addEventListener('DOMContentLoaded', () => {
  if (!document.body.classList.contains('woocommerce-checkout')) return;

  const checkoutForm    = document.querySelector('form.checkout');
  const customerDetails = document.querySelector('#customer_details');
  const orderReview     = document.querySelector('#order_review');
  if (!checkoutForm || !customerDetails || !orderReview) return;

  const needsShipping = () => !!orderReview.querySelector('#shipping_method, .woocommerce-shipping-totals');

  // Build single-column wrapper
  const wrapper = document.createElement('div');
  wrapper.className = 'wc-checkout';
  checkoutForm.prepend(wrapper);

  // Stepper nav + error summary
  const stepsNav = document.createElement('ul');
  stepsNav.className = 'wc-steps';
  stepsNav.setAttribute('role', 'tablist');

  const errSummary = document.createElement('div');
  errSummary.id = 'wc-error-summary';
  errSummary.hidden = true;
  errSummary.setAttribute('tabindex', '-1');
  errSummary.innerHTML = `<strong>There’s a problem:</strong><ul></ul>`;

  const makeStep = (id, title, nodes = []) => {
    const panel = document.createElement('section');
    panel.className = 'wc-step';
    panel.id = id;
    panel.setAttribute('role', 'tabpanel');
    panel.setAttribute('aria-labelledby', `${id}-tab`);
    const h = document.createElement('h3');
    h.className = 'wc-step__title';
    h.textContent = title;
    const body = document.createElement('div');
    body.className = 'wc-step__content';
    nodes.forEach(n => n && body.append(n));
    const actions = document.createElement('div');
    actions.className = 'wc-step__actions';
    panel.append(h, body, actions);
    return { panel, body, actions, heading: h };
  };
  const makeTab = (id, label, index) => {
    const li = document.createElement('li');
    li.className = 'wc-steps__item';
    const btn = document.createElement('button');
    btn.className = 'wc-steps__btn';
    btn.type = 'button';
    btn.id = `${id}-tab`;
    btn.setAttribute('role', 'tab');
    btn.setAttribute('aria-controls', id);
    btn.setAttribute('data-step-index', String(index));
    btn.textContent = `${index + 1}. ${label}`;
    li.append(btn);
    return { li, btn };
  };

  const col1 = customerDetails.querySelector('.col-1');
  const col2 = customerDetails.querySelector('.col-2');
  const billingFields  = col1?.querySelector('.woocommerce-billing-fields');
  const shippingFields = col2?.querySelector('.woocommerce-shipping-fields');

  const steps = [];
  const tabs  = [];

  // Step 1 — Your details (billing)
  const step1 = makeStep('wc-step-billing', 'Enter your details');
  if (billingFields) step1.body.append(billingFields);
  steps.push(step1.panel);
  tabs.push(makeTab('wc-step-billing', 'Your details', 0));

  // Step 2 — Recipient (shipping)
  const step2 = makeStep('wc-step-shipping', 'Recipient details');
  if (shippingFields) step2.body.append(shippingFields);
  steps.push(step2.panel);
  tabs.push(makeTab('wc-step-shipping', 'Recipient', 1));

  // Step 3 — Confirm (order review + shipping methods + payment)
  const step3 = makeStep('wc-step-confirm', 'Confirm your Order', []);
  step3.body.append(orderReview);

  // Coupon toggle if hidden by theme
  const existingToggle = document.querySelector('.woocommerce-form-coupon-toggle');
  const couponForm     = document.querySelector('form.checkout_coupon, .checkout_coupon, .woocommerce-form-coupon');
  if (!existingToggle && couponForm) {
    const t = document.createElement('p');
    t.className = 'wc-coupon-toggle';
    t.innerHTML = `Have a coupon? <a href="#" id="wc-show-coupon">Click here to enter it</a>.`;
    step3.body.prepend(t);
    t.querySelector('#wc-show-coupon').addEventListener('click', (e) => {
      e.preventDefault();
      couponForm.style.display = '';
      couponForm.removeAttribute('hidden');
      couponForm.classList.remove('woocommerce-hidden', 'hidden');
      couponForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
      couponForm.querySelector('input[name="coupon_code"]')?.focus();
    });
  }

  steps.push(step3.panel);
  tabs.push(makeTab('wc-step-confirm', 'Confirm', 2));

  // Assemble
  tabs.forEach(t => stepsNav.append(t.li));
  wrapper.append(stepsNav, errSummary);
  steps.forEach(s => wrapper.append(s));
  customerDetails.remove(); // moved children

  // Controls
  const addActions = (panel, index) => {
    const actions = panel.querySelector('.wc-step__actions');
    if (index > 0) {
      const back = document.createElement('button');
      back.type = 'button';
      back.className = 'wc-btn wc-btn--secondary';
      back.textContent = 'Back';
      back.addEventListener('click', () => goTo(index - 1));
      actions.append(back);
    } else {
      actions.append(document.createElement('span'));
    }
    const next = document.createElement('button');
    next.type = 'button';
    next.className = 'wc-btn';
    next.textContent = index === steps.length - 1 ? 'Place Order' : 'Next';
    next.addEventListener('click', () => {
      if (!validateStep(steps[index])) return;
      if (index < steps.length - 1) {
        goTo(index + 1);
        if (window.jQuery) window.jQuery(document.body).trigger('update_checkout');
      } else {
        checkoutForm.requestSubmit();
      }
    });
    actions.append(next);
  };
  steps.forEach((panel, i) => addActions(panel, i));

  // Nav state
  let current = 0;

  function goTo(idx) {
    idx = computeSkips(idx);

    tabs.forEach(({ btn }, i) => {
      btn.setAttribute('aria-current', i === idx ? 'step' : 'false');
      btn.disabled = i > idx + 1; // allow back and immediate next
    });
    steps.forEach((panel, i) => { panel.hidden = i !== idx; });
    current = idx;
    document.body.classList.toggle('wc-step--confirm-active', current === 2);
    history.replaceState(null, '', `#step-${idx + 1}`);
  }

  // Allow clicking next tab (validate), clicking back freely
  tabs.forEach(({ btn }) => {
    btn.addEventListener('click', () => {
      const idx = parseInt(btn.getAttribute('data-step-index'), 10);
      if (idx <= current) { goTo(idx); return; }
      if (idx === current + 1) {
        if (!validateStep(steps[current])) return;
        goTo(idx);
        if (window.jQuery) window.jQuery(document.body).trigger('update_checkout');
      }
    });
  });

  function computeSkips(idx) {
    const virtualOnly = !needsShipping();
    const shipToggle = document.querySelector('#ship-to-different-address input[type="checkbox"]');
    const shippingRequired = !!document.querySelector('.woocommerce-shipping-fields');
    const shippingAddressVisible = shippingRequired && shipToggle && shipToggle.checked;

    if (idx === 1 && (virtualOnly || !shippingAddressVisible)) return 2;
    return idx;
  }

  function validateStep(panel) {
    errSummary.hidden = true;
    const list = errSummary.querySelector('ul'); list.innerHTML = '';
    const requiredVisible = Array.from(panel.querySelectorAll('input,select,textarea'))
      .filter(el => el.closest('[hidden]') === null)
      .filter(el => el.hasAttribute('required') || el.classList.contains('validate-required'));
    const errors = [];
    requiredVisible.forEach(el => {
      const val = (el.value || '').trim();
      const type = el.getAttribute('type');
      let bad = !val;
      if (!bad && type === 'email') bad = !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);
      if (!bad && el.id === 'billing_postcode') bad = !/^[0-9]{3,4}$/.test(val);
      if (bad) {
        errors.push({ el, msg: labelFor(el) + ' is required' });
        el.setAttribute('aria-invalid', 'true');
      } else {
        el.removeAttribute('aria-invalid');
      }
    });
    if (errors.length) {
      const ul = errSummary.querySelector('ul');
      errors.forEach(e => { const li = document.createElement('li'); li.textContent = e.msg; ul.append(li); });
      errSummary.hidden = false; errSummary.focus(); errors[0].el.focus();
      return false;
    }
    return true;
  }
  function labelFor(el) {
    const id = el.getAttribute('id');
    const lab = id ? document.querySelector(`label[for="${id}"]`) : null;
    return lab ? lab.textContent.replace('*', '').trim() : (el.name || 'This field');
  }

  // Init state
  steps.forEach((p, i) => { if (i !== 0) p.hidden = true; });
  tabs.forEach(({ btn }, i) => { btn.disabled = i > 1; if (i === 0) btn.setAttribute('aria-current', 'step'); });

  const m = (location.hash || '').match(/#step-(\d+)/);
  const start = m ? Math.max(0, Math.min(steps.length - 1, parseInt(m[1], 10) - 1)) : 0;
  goTo(start);

  // Reveal with cross-fade: swap wc-prep -> wc-ready and focus first name
  document.documentElement.classList.remove('wc-prep');
  document.documentElement.classList.add('wc-ready');

  const focusTarget =
    document.querySelector('#billing_first_name') ||
    document.querySelector('#billing_firstname') ||
    document.querySelector('[name="billing_first_name"]') ||
    document.querySelector('[name="billing_firstname"]') ||
    step1.panel.querySelector('input, select, textarea');

  setTimeout(() => { focusTarget && focusTarget.focus(); }, 30);

  // Keep totals fresh on address-ish changes
  const refreshCheckout = () => { if (window.jQuery) window.jQuery(document.body).trigger('update_checkout'); };
  checkoutForm.addEventListener('change', (e) => {
    const t = e.target; if (!t) return;
    if (t.name && /billing_|shipping_|ship_to_different_address|country|state|postcode|city|address/i.test(t.name)) {
      refreshCheckout();
    }
  });
});
