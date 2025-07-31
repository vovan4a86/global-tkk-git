export const truncateText = ({ el, maxlength }) => {
  const data = document.querySelectorAll(el);

  data?.forEach(str => {
    const tmp = str.textContent;
    if (tmp) str.textContent = truncate(tmp, maxlength);
  });

  function truncate(str, maxlength) {
    return str.length > maxlength ? str.slice(0, maxlength - 1) + '…' : str;
  }
};

truncateText({
  el: '.class',
  maxlength: 60
});
