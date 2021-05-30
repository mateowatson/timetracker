import qs from 'qs';

export async function ajaxFormSubmit(event) {
  event.preventDefault();
  const formEl = event.target;

  let data = {};
  for (const pair of new FormData(formEl)) {
    data[pair[0]] = pair[1];
  }

  data = qs.stringify(data);

  const url = formEl.getAttribute('action');

  const myHeaders = new Headers();
  myHeaders.append('Content-Type', 'application/x-www-form-urlencoded');

  const config = {
    method: 'POST',
    headers: myHeaders,
    body: data
  };

  try {
    // submit the form
    const response = await fetch(url, config);
    // get the body from the response
    const body = await response.text();

    // perform a body swap
    document.querySelector('body').innerHTML = body;
  } catch (err) {
    console.error(err);
  }
}