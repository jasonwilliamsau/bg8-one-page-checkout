document.addEventListener('DOMContentLoaded', () => {
  if (!document.body.classList.contains('woocommerce-checkout')) return;

  // Skip loader functionality in page builders
  const isPageBuilder = document.body.classList.contains('oxygen-body') ||
                       document.body.classList.contains('elementor-editor-active') ||
                       document.body.classList.contains('et-fb') ||
                       document.body.classList.contains('brxe-editor') ||
                       document.body.classList.contains('fl-builder') ||
                       document.body.classList.contains('wp-admin');

  const checkoutForm    = document.querySelector('form.checkout');
  const customerDetails = document.querySelector('#customer_details');
  const orderReview     = document.querySelector('#order_review');
  if (!checkoutForm || !customerDetails || !orderReview) return;

  const needsShipping = () => !!orderReview.querySelector('#shipping_method, .woocommerce-shipping-totals');

  // Build single-column wrapper
  const wrapper = document.createElement('div');
  wrapper.className = 'wc-checkout';
  checkoutForm.prepend(wrapper);

  // Create header (will be populated by PHP)
  const header = document.createElement('div');
  header.className = 'wc-checkout-header';
  header.innerHTML = `
    <h1 class="wc-checkout-title"></h1>
    <p class="wc-checkout-subtitle"></p>
  `;

  // Create steps indicator
  const stepsIndicator = document.createElement('div');
  stepsIndicator.className = 'wc-steps-indicator';

  // Create content wrapper
  const content = document.createElement('div');
  content.className = 'wc-checkout-content';

  const errSummary = document.createElement('div');
  errSummary.id = 'wc-error-summary';
  errSummary.hidden = true;
  errSummary.setAttribute('tabindex', '-1');
  errSummary.innerHTML = `<strong>There's a problem:</strong><ul></ul>`;

  const makeStep = (id, title, nodes = []) => {
    const panel = document.createElement('section');
    panel.className = 'wc-step-content';
    panel.id = id;
    panel.setAttribute('role', 'tabpanel');
    panel.setAttribute('aria-labelledby', `${id}-tab`);
    
    const formSection = document.createElement('div');
    formSection.className = 'wc-form-section';
    
    const h = document.createElement('h3');
    h.textContent = title;
    
    const body = document.createElement('div');
    nodes.forEach(n => n && body.append(n));
    
    const actions = document.createElement('div');
    actions.className = 'wc-checkout-actions';
    
    formSection.append(h, body);
    panel.append(formSection, actions);
    return { panel, body, actions, heading: h };
  };
  const makeTab = (id, label, index) => {
    const step = document.createElement('div');
    step.className = 'wc-step';
    step.id = `${id}-tab`;
    step.setAttribute('role', 'tab');
    step.setAttribute('aria-controls', id);
    step.setAttribute('data-step-index', String(index));
    
    const number = document.createElement('div');
    number.className = 'wc-step-number';
    number.textContent = index + 1;
    
    const stepLabel = document.createElement('div');
    stepLabel.className = 'wc-step-label';
    stepLabel.textContent = label;
    
    const arrow = document.createElement('div');
    arrow.className = 'wc-step-arrow';
    arrow.textContent = '→';
    
    step.append(number, stepLabel, arrow);
    return { step, number, stepLabel };
  };

  const col1 = customerDetails.querySelector('.col-1');
  const col2 = customerDetails.querySelector('.col-2');
  const billingFields  = col1?.querySelector('.woocommerce-billing-fields');
  const shippingFields = col2?.querySelector('.woocommerce-shipping-fields');
  const additionalFields = customerDetails.querySelector('.woocommerce-additional-fields') || 
                           checkoutForm.querySelector('.woocommerce-additional-fields');

  const steps = [];
  const tabs  = [];

  // Step 1 — Your details (billing)
  const step1 = makeStep('wc-step-billing', 'Enter your details');
  if (billingFields) step1.body.append(billingFields);
  if (additionalFields) step1.body.append(additionalFields);
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
  tabs.forEach(t => stepsIndicator.append(t.step));
  content.append(errSummary);
  steps.forEach(s => content.append(s));
  
  // Configure header from PHP settings
  const config = window.bg8CheckoutConfig || {};
  const titleEl = header.querySelector('.wc-checkout-title');
  const descEl = header.querySelector('.wc-checkout-subtitle');
  
  if (config.title) titleEl.textContent = config.title;
  if (config.description) descEl.textContent = config.description;
  
  // Show header only if at least one field has content
  const showHeader = config.showHeader === true;
  if (showHeader) {
    wrapper.append(header, stepsIndicator, content);
  } else {
    wrapper.append(stepsIndicator, content);
  }
  customerDetails.remove(); // moved children

  // Controls
  const addActions = (panel, index) => {
    const actions = panel.querySelector('.wc-checkout-actions');
    if (index > 0) {
      const back = document.createElement('button');
      back.type = 'button';
      back.className = 'wc-btn wc-btn-secondary';
      back.textContent = '← Back';
      back.addEventListener('click', () => goTo(index - 1));
      actions.append(back);
    } else {
      actions.append(document.createElement('span'));
    }
    const next = document.createElement('button');
    next.type = 'button';
    next.className = 'wc-btn wc-btn-primary';
    next.textContent = index === steps.length - 1 ? 'Place Order' : 'Continue →';
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

    tabs.forEach(({ step }, i) => {
      step.classList.toggle('active', i === idx);
      step.classList.toggle('completed', i < idx);
    });
    steps.forEach((panel, i) => { 
      panel.classList.toggle('active', i === idx);
      panel.style.display = i === idx ? 'block' : 'none';
    });
    current = idx;
    document.body.classList.toggle('wc-step--confirm-active', current === 2);
    history.replaceState(null, '', `#step-${idx + 1}`);
  }

  // Allow clicking next tab (validate), clicking back freely
  tabs.forEach(({ step }) => {
    step.addEventListener('click', () => {
      const idx = parseInt(step.getAttribute('data-step-index'), 10);
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
  steps.forEach((p, i) => { 
    if (i !== 0) {
      p.style.display = 'none';
    } else {
      p.classList.add('active');
    }
  });
  tabs.forEach(({ step }, i) => { 
    if (i === 0) {
      step.classList.add('active');
    }
  });

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
