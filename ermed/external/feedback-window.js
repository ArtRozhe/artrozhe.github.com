'use strict';

// Feedback window class

function Feedback(options) {
  var feedbackWindow;

  function open() {
    if (!feedbackWindow) {
      render();
    }
    else {
      if (feedbackWindow.style.display !== 'block') {
        feedbackWindow.style.display = 'block';
        document.body.appendChild(getBackground());
      }
    }
    return feedbackWindow;
  }

  function close() {
    var background;
    if (feedbackWindow) {
      if (feedbackWindow.style.display = 'block') {
        background = document.getElementById('background');
        if (background) {
          background.parentNode.removeChild(background);
        }
        feedbackWindow.style.display = 'none';
        clearValidationErrorMessages();
        clearInputsValues();
      }
    }
  }

  function render() {
    var background = document.createElement('div'), // слой затемнения
        feedbackForm = getFeedbackForm(),
        telMask = '9 (9999) 99-99-99',
        tel;

    feedbackWindow = getModalWindow(feedbackForm);

    background.id = 'background'; // id чтобы подхватить стиль
    feedbackWindow.className = 'modal-window';

    document.body.appendChild(background); // включаем затемнение
    document.body.appendChild(feedbackWindow); // добавляем модальное окно

    tel = document.getElementById('feedbackPhone');
    VMasker(tel).maskPattern(telMask);

    feedbackWindow.style.display = 'block'; // "включаем" его

    background.onclick = function () {  // при клике на слой затемнения все исчезнет
        close();
        return false;
    };
  }



  function getModalWindow(content) {
    var modalWindow = document.createElement('div'),
        modalHeader = document.createElement('div'),
        modalBody = document.createElement('div'),
        modalTitle = document.createElement('h3'),
        closeCross = document.createElement('button');

    modalWindow.className = 'modal-window';
    modalHeader.className = 'modal-header';
    modalBody.className = 'modal-body';
    modalTitle.className = 'modal-title';
    closeCross.className = 'close-cross';

    closeCross.innerHTML = 'x';
    modalTitle.innerHTML = 'Форма обратной связи';

    modalHeader.appendChild(closeCross);
    modalHeader.appendChild(modalTitle);

    if (content) {
      modalBody.appendChild(content);
    }

    modalWindow.appendChild(modalHeader);
    modalWindow.appendChild(modalBody);

    closeCross.addEventListener('click', function() {
      close();
    });

    return modalWindow;
  }

  function getFeedbackForm() {
    var form = document.createElement('form');
        /*inputFio = document.createElement('input'),
        inputPhone = document.createElement('input'),
        textareaMessage = document.createElement('textarea');

    form.className = 'feedback-form';

    inputFio.placeholder = 'ФИО';
    inputPhone.placeholder = 'Телефон';
    textareaMessage.placeholder = 'Текст сообщения';

    form.appendChild(inputFio);
    form.appendChild(inputPhone);
    form.appendChild(textareaMessage);

    form.appendChild(getControlButtons());*/

    form.className = 'feedback-form';
    form.innerHTML = '<fieldset><div class="control-group"><label class="control-label" for="feedbackFIO">Ваше ФИО: <span class="star">*</span></label><div class="controls"><input class="feedback-inputs" type="text" tabindex="1" placeholder="Иванов Иван Иванович" name="feedbackFIO" id="feedbackFIO" autocomplete="off" autofocus="true" required></div></div><div class="control-group"><label class="control-label" for="feedbackPhone">Ваш телефон: <span class="star">*</span></label><div class="controls"><input class="feedback-inputs" type="text" placeholder="Тел.: 8 (3822) 11-11-11" name="feedbackPhone" id="feedbackPhone" autocomplete="off" required></div></div><div class="control-group"><label class="control-label" for="feedbackText">Текст: <span class="star">*</span></label><div class="controls"><textarea class="feedback-inputs" rows="5" name="feedbackText" id="feedbackText" required></textarea></div></div></fieldset>'
    form.appendChild(getControlButtons());
    return form;
  }

  function getControlGroupOfForm(settings) {

  }

  function getControlButtons() {
    var controlButtons = document.createElement('div'),
        submitButton = document.createElement('input'),
        closeButton = document.createElement('button');

    controlButtons.className = 'control-buttons';
    submitButton.className = 'submit-button btn';
    closeButton.className = 'close-button btn btn-primary';

    submitButton.type = 'submit';
    closeButton.type = 'button';

    closeButton.innerHTML = 'Закрыть';

    controlButtons.appendChild(submitButton);
    controlButtons.appendChild(closeButton);

    closeButton.addEventListener('click', function() {
      close();
    });

    submitButton.addEventListener('click', function(event) {
      event.preventDefault();
      validateInputs();
    });

    return controlButtons;
  }

  function validateInputs() {
    var inputs = document.querySelectorAll('.feedback-inputs'),
        i;

    clearValidationErrorMessages();
    
    for (i = 0; i < inputs.length; i++) {

      if (!checkInput(inputs[i])) {
        var message = createErrorMessage(inputs[i]);
        inputs[i].parentNode.appendChild(message);
      }
    }
  }

  function clearValidationErrorMessages() {
    var messages = document.querySelectorAll('.validate-error'),
        i;

    for (i = 0; i < messages.length; i++) {
      messages[i].parentNode.removeChild(messages[i]);
    }
  }

  function clearInputsValues() {
    var inputs = document.querySelectorAll('.feedback-inputs'),
    i;

    for (i = 0; i < inputs.length; i++) {
      inputs[i].value = '';
    }
  }

  function createErrorMessage(input) {

    if (!input) {
      return false;
    }

    var message = document.createElement('span'),
        textFio = 'Неправильные данные в поле ФИО. Вводите только буквы русского алфавита. Убедитесь, что присутствуют хотя бы фамилия и имя.',
        textPhone = 'Введите номер телефона в правильном формате.',
        textMessage = 'Введите текст сообщения. Не менее 10 символов.';

    message.className = 'validate-error';

    switch(input.id){
      case 'feedbackFIO':
        message.innerHTML = textFio;
        break;
      case 'feedbackPhone':
        message.innerHTML = textPhone;
        break;
      case 'feedbackText':
        message.innerHTML = textMessage;
        break;
      default:
        message.innerHTML = 'Заполните поле.';
        break
    }

    return message;
      
  }

  function checkInput(input) {
    var regExpForFio = new RegExp('^[А-ЯЁ][а-яё]+(-[А-ЯЁ][а-яё]+)? [А-ЯЁ][а-яё]+( [А-ЯЁ][а-яё]+)?$'),
        result = false;

    switch(input.id) {
      case 'feedbackFIO':
        if (regExpForFio.test(input.value)) {
          result = true;
        }
        break;
      case 'feedbackPhone':
        if (input.value.length === 17) {
          result = true;
        }
        break;
      case 'feedbackText':
        if (input.value.length >= 10) {
          result = true;
        }
        break;
      default:
        result = true;
        break;
    }

    return result;
  }

  function getBackground() {
    var background = document.createElement('div');
    background.id = 'background';

    background.onclick = function () {  // при клике на слой затемнения все исчезнет
      background.parentNode.removeChild(background); // удаляем затемнение
      feedbackWindow.style.display = 'none'; // делаем окно невидимым
      return false;
    };

    return background;
  }

  this.open = open;
};
