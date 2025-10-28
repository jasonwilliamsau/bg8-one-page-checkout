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
  
  // Check if pickup/delivery first is enabled
  const config = window.bg8CheckoutConfig || {};
  const pickupDeliveryFirst = config.pickupDeliveryFirst === true || config.pickupDeliveryFirst === 'true';
  const pickupShippingMethod = config.pickupShippingMethod || '';
  const deliveryShippingMethod = config.deliveryShippingMethod || '';
  let pickupDeliveryChoice = null;
  
  // Create pickup/delivery selection step if enabled
  let pickupDeliveryStep = null;
  let pickupDeliveryTab = null;
  if (pickupDeliveryFirst) {
    pickupDeliveryStep = makeStep('wc-step-pickup-delivery', 'How would you like to receive your order?');
    const choiceContainer = document.createElement('div');
    choiceContainer.className = 'wc-pickup-delivery-choice';
    
    const pickupBtn = document.createElement('button');
    pickupBtn.type = 'button';
    pickupBtn.className = 'wc-choice-btn';
    pickupBtn.dataset.choice = 'pickup';
    pickupBtn.innerHTML = '<span class="wc-choice-icon">💐</span><span class="wc-choice-text">Pickup</span><span class="wc-choice-desc">Collect from store</span>';
    
    const deliveryBtn = document.createElement('button');
    deliveryBtn.type = 'button';
    deliveryBtn.className = 'wc-choice-btn';
    deliveryBtn.dataset.choice = 'delivery';
    deliveryBtn.innerHTML = '<span class="wc-choice-icon">🚚</span><span class="wc-choice-text">Delivery</span><span class="wc-choice-desc">Deliver to my address</span>';
    
    const handleChoice = (choice, skipVisibilityUpdate = false) => {
      pickupDeliveryChoice = choice;
      pickupBtn.classList.toggle('selected', choice === 'pickup');
      deliveryBtn.classList.toggle('selected', choice === 'delivery');
      
      // Update recipient step visibility (but skip if step2 not ready yet)
      if (!skipVisibilityUpdate) {
        updateRecipientStepVisibility(choice);
      }
      
      // Hide "Ship to different address" if pickup
      const shipToggle = document.querySelector('#ship-to-different-address');
      if (shipToggle) {
        if (choice === 'pickup') {
          shipToggle.style.display = 'none';
          const checkbox = shipToggle.querySelector('input[type="checkbox"]');
          if (checkbox) checkbox.checked = false;
        } else {
          shipToggle.style.display = '';
        }
      }
      
      // Pre-select the correct shipping method
      const shippingMethods = document.querySelectorAll('input[name^="shipping_method"]');
      if (shippingMethods.length > 0) {
        let targetMethod = null;
        
        // Check if admin has configured specific shipping methods
        const configuredMethod = choice === 'pickup' ? pickupShippingMethod : deliveryShippingMethod;
        
        if (configuredMethod) {
          // Use the admin-configured shipping method
          shippingMethods.forEach(method => {
            const methodValue = method.value || '';
            if (methodValue === configuredMethod) {
              targetMethod = method;
            }
          });
        } else {
          // Auto-detect shipping method
          shippingMethods.forEach(method => {
            const methodId = method.value || method.id || '';
            const methodLabel = method.parentElement?.textContent || '';
            
            if (choice === 'pickup') {
              // Look for local_pickup or similar
              if (methodId.includes('local_pickup') || methodId.includes('pickup') || 
                  methodLabel.toLowerCase().includes('pickup') || methodLabel.toLowerCase().includes('collection')) {
                targetMethod = method;
              }
            } else {
              // Look for delivery methods (not pickup)
              if (!methodId.includes('local_pickup') && !methodId.includes('pickup') &&
                  !methodLabel.toLowerCase().includes('pickup') && !methodLabel.toLowerCase().includes('collection')) {
                if (!targetMethod) { // Select first non-pickup method
                  targetMethod = method;
                }
              }
            }
          });
        }
        
        // Select the target method if found
        if (targetMethod && !targetMethod.checked) {
          targetMethod.checked = true;
          targetMethod.click(); // Trigger click to ensure WooCommerce processes it
          
          // Trigger WooCommerce checkout update
          if (window.jQuery) {
            window.jQuery(document.body).trigger('update_checkout');
          }
        }
      }
      
      // Make delivery button active
      if (choice === 'pickup') {
        deliveryBtn.disabled = false;
      } else {
        deliveryBtn.disabled = false;
      }
    };
    
    pickupBtn.addEventListener('click', () => handleChoice('pickup'));
    deliveryBtn.addEventListener('click', () => handleChoice('delivery'));
    
    choiceContainer.append(pickupBtn, deliveryBtn);
    pickupDeliveryStep.body.append(choiceContainer);
    steps.push(pickupDeliveryStep.panel);
    pickupDeliveryTab = makeTab('wc-step-pickup-delivery', 'Choose', 0);
    tabs.push(pickupDeliveryTab);
    
    // Pre-select delivery (skip visibility update until step2 is created)
    pickupDeliveryChoice = 'delivery';
    handleChoice('delivery', true);
  }
  
  // Function to update recipient step visibility based on pickup/delivery choice
  function updateRecipientStepVisibility(choice) {
    if (!pickupDeliveryFirst || !step2) return;
    
    const isPickup = choice === 'pickup';
    
    // Hide/show the recipient step panel
    if (step2.panel.parentNode) {
      if (isPickup) {
        step2.panel.style.display = 'none';
        step2.panel.classList.add('hidden-step');
      } else {
        // Remove hidden class - goTo will handle display
        step2.panel.classList.remove('hidden-step');
      }
    }
    
    // Hide/show the recipient tab indicator
    const recipientTab = tabs[pickupDeliveryFirst ? 2 : 1];
    if (recipientTab) {
      if (isPickup) {
        recipientTab.step.style.display = 'none';
        recipientTab.step.classList.add('hidden-tab');
      } else {
        recipientTab.step.style.removeProperty('display');
        recipientTab.step.classList.remove('hidden-tab');
      }
    }
    
    // Update step numbers for visible steps
    let stepNumber = 1;
    tabs.forEach((tab, index) => {
      // Check both display style and hidden-tab class
      const isHidden = tab.step.classList.contains('hidden-tab') || 
                      tab.step.style.display === 'none';
      if (!isHidden) {
        tab.number.textContent = stepNumber;
        stepNumber++;
      }
    });
  }
  
  // Initialize recipient step visibility after all steps are created
  // Use setTimeout to ensure DOM is ready
  if (pickupDeliveryFirst) {
    setTimeout(() => {
      if (step2 && pickupDeliveryChoice) {
        updateRecipientStepVisibility(pickupDeliveryChoice);
      }
    }, 100);
  }

  // Step 1 — Your details (billing)
  const step1 = makeStep('wc-step-billing', 'Enter your details');
  if (billingFields) step1.body.append(billingFields);
  if (additionalFields) step1.body.append(additionalFields);
  steps.push(step1.panel);
  tabs.push(makeTab('wc-step-billing', 'Your details', pickupDeliveryFirst ? 1 : 0));

  // Step 2 — Recipient (shipping)
  const step2 = makeStep('wc-step-shipping', 'Recipient details');
  if (shippingFields) step2.body.append(shippingFields);
  steps.push(step2.panel);
  tabs.push(makeTab('wc-step-shipping', 'Recipient', pickupDeliveryFirst ? 2 : 1));

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
  tabs.push(makeTab('wc-step-confirm', 'Confirm', pickupDeliveryFirst ? 3 : 2));

  // Assemble
  tabs.forEach(t => stepsIndicator.append(t.step));
  content.append(errSummary);
  steps.forEach(s => content.append(s));
  
  // Configure header from PHP settings (already defined above)
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
    
    // Special handling for pickup/delivery step
    if (pickupDeliveryStep && panel === pickupDeliveryStep.panel) {
      actions.innerHTML = ''; // Clear default actions
      const next = document.createElement('button');
      next.type = 'button';
      next.className = 'wc-btn wc-btn-primary';
      next.textContent = 'Continue →';
      next.disabled = !pickupDeliveryChoice;
      next.addEventListener('click', () => {
        if (!pickupDeliveryChoice) return;
        if (window.jQuery) window.jQuery(document.body).trigger('update_checkout');
        goTo(index + 1);
      });
      actions.append(next);
      return;
    }
    
    if (index > 0) {
      const back = document.createElement('button');
      back.type = 'button';
      back.className = 'wc-btn wc-btn-secondary';
      back.textContent = '← Back';
      back.addEventListener('click', () => {
        // Find the previous visible step
        let prevIdx = index - 1;
        while (prevIdx >= 0 && steps[prevIdx].classList.contains('hidden-step')) {
          prevIdx--;
        }
        if (prevIdx >= 0) {
          goTo(prevIdx);
        }
      });
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
        // Find the next visible step
        let nextIdx = index + 1;
        while (nextIdx < steps.length && steps[nextIdx].classList.contains('hidden-step')) {
          nextIdx++;
        }
        if (nextIdx < steps.length) {
          goTo(nextIdx);
        } else {
          checkoutForm.requestSubmit();
        }
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

    // Get actual indices accounting for hidden steps
    const visibleIndices = [];
    tabs.forEach((tab, i) => {
      if (!tab.step.classList.contains('hidden-step') && 
          (!tab.step.style.display || tab.step.style.display !== 'none')) {
        visibleIndices.push(i);
      }
    });

    tabs.forEach(({ step }, i) => {
      step.classList.toggle('active', i === idx);
      step.classList.toggle('completed', i < idx);
    });
    
    steps.forEach((panel, i) => { 
      panel.classList.toggle('active', i === idx);
      
      const isHidden = panel.classList.contains('hidden-step');
      const isCurrent = i === idx;
      
      // Show current step if not hidden, hide everything else
      if (isCurrent && !isHidden) {
        panel.style.display = 'block';
      } else {
        panel.style.display = 'none';
      }
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
    // If pickup/delivery first is enabled and pickup is chosen, skip the shipping step
    if (pickupDeliveryFirst && pickupDeliveryChoice === 'pickup') {
      // When coming from billing (step 1), go to confirm (step 3) instead of shipping (step 2)
      if (idx === 2) return 3;
    }
    
    // If pickup/delivery first is enabled and delivery is chosen, always show the shipping step
    if (pickupDeliveryFirst && pickupDeliveryChoice === 'delivery') {
      // Don't skip the recipient step when delivery is selected
      return idx;
    }
    
    const virtualOnly = !needsShipping();
    const shipToggle = document.querySelector('#ship-to-different-address input[type="checkbox"]');
    const shippingRequired = !!document.querySelector('.woocommerce-shipping-fields');
    const shippingAddressVisible = shippingRequired && shipToggle && shipToggle.checked;
    
    // Original logic for virtual orders or when "ship to different address" is unchecked
    if (idx === (pickupDeliveryFirst ? 2 : 1) && (virtualOnly || !shippingAddressVisible)) {
      return idx + 1;
    }
    
    return idx;
  }

  function validateStep(panel) {
    errSummary.hidden = true;
    const list = errSummary.querySelector('ul'); list.innerHTML = '';
    
    // Skip validation for pickup/delivery step - it's handled by button disabled state
    if (pickupDeliveryStep && panel === pickupDeliveryStep.panel) {
      return true;
    }
    
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
  
  // Update button disabled state for pickup/delivery step
  if (pickupDeliveryStep) {
    const observeChoiceButtons = () => {
      const continueBtn = pickupDeliveryStep.actions.querySelector('.wc-btn-primary');
      if (continueBtn) {
        continueBtn.disabled = !pickupDeliveryChoice;
      }
    };
    
    // Observe changes to pickupDeliveryChoice
    const choiceObserver = new MutationObserver(observeChoiceButtons);
    choiceObserver.observe(pickupDeliveryStep.panel, { childList: true, subtree: true });
    
    // Also listen for button clicks
    pickupDeliveryStep.panel.querySelectorAll('.wc-choice-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        setTimeout(observeChoiceButtons, 100);
      });
    });
  }

  // Reveal with cross-fade: swap wc-prep -> wc-ready and focus first name
  document.documentElement.classList.remove('wc-prep');
  document.documentElement.classList.add('wc-ready');

  let focusTarget = null;
  if (pickupDeliveryStep && pickupDeliveryChoice === null) {
    focusTarget = pickupDeliveryStep.panel.querySelector('.wc-choice-btn');
  } else {
    focusTarget =
      document.querySelector('#billing_first_name') ||
      document.querySelector('#billing_firstname') ||
      document.querySelector('[name="billing_first_name"]') ||
      document.querySelector('[name="billing_firstname"]') ||
      step1.panel.querySelector('input, select, textarea');
  }

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
