// Options for feedback window

var options = {
  enabledCaptcha: true,
  uriSubmit: 'https://vk.com/' // for example
}

// Feedback window initialization

var feedback = new Feedback(options),
    button = document.getElementsByClassName("feedback-window")[0];

button.addEventListener('click', function() {
  feedback.open();
});