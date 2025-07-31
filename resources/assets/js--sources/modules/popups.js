import { Fancybox } from '@fancyapps/ui';

export const closeBtn =
  '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M25 7 7 25M25 25 7 7"/></svg>';

Fancybox.bind('[data-fancybox]', {
  showClass: 'f-fadeIn',
  hideClass: 'f-fadeOut',
  Carousel: {
    infinite: false
  }
});

Fancybox.bind('[data-popup]', {
  mainClass: 'popup--custom',
  template: { closeButton: closeBtn },
  showClass: 'f-fadeIn',
  hideClass: 'f-fadeOut',
  on: {
    reveal: (e, trigger) => {
      // отправка данных в попап из кнопки
      const { name } = trigger;
      const { job } = trigger;
      const popup = trigger.contentEl;
      const popupInput = popup.querySelector('[data-service-field]');
      const popupText = popup.querySelector('.popup__text');

      // наименование в input
      if (name && popupInput) popupInput.value = name || '';

      // вакансия в input и text
      if (job && popupText) {
        popupInput.value = job || '';
        popupText.textContent = job;
      }
    }
  }
});

Fancybox.bind('[data-cities]', {
  closeButton: 'outside',
  mainClass: 'popup--custom popup--ajax',
  showClass: 'f-fadeIn',
  hideClass: 'f-fadeOut',
  template: { closeButton: closeBtn }
});

export const showSuccessRequestDialog = ({ title, text }) => {
  Fancybox.show([{ src: '#request-done', type: 'inline' }], {
    mainClass: 'popup--custom popup--complete',
    template: { closeButton: closeBtn },
    showClass: 'f-fadeIn',
    hideClass: 'f-fadeOut',
    on: {
      reveal: (e, trigger) => {
        const popup = trigger.contentEl;
        const popupTitle = popup.querySelector('.popup__title');
        const popupText = popup.querySelector('.popup__text');

        if (popupTitle) popupTitle.textContent = title || '';
        if (popupText) popupText.textContent = text || '';
      }
    }
  });
};

// в свой модуль форм, импортируешь функцию вызова «спасибо» → вызываешь on success
// import { showSuccessRequestDialog } from 'путь до компонента'
// вызываешь где нужно
// showSuccessRequestDialog({
//   title: 'Ваше сообщение успешно отправлено!',
//   text: 'Мы свяжемся с вами в ближайшее время!'
// });
