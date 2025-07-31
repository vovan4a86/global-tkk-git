import 'focus-visible';
import './modules';
import { maskedInputs } from './modules/inputMask';
import { utils } from './modules/utility';

utils();

maskedInputs({
  phoneSelector: 'input[name="phone"]',
  emailSelector: 'input[name="email"]'
});
