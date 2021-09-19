import $ from 'cash-dom';
import qs from 'qs';

/**
 * Attaches event listeners
 */
export function initAjax() {
  initAjaxLinks();
}

/**
 * Event handler for form submissions using fetch. It gets the response and does
 * a body swap.
 * 
 * @param {Event} event 
 */
export async function ajaxFormSubmit(event) {
  event.preventDefault();
  const formEl = event.target;

  // get the data from the form
  let data = {};
  for (const pair of new FormData(formEl)) {
    data[pair[0]] = pair[1];
  }

  // convert to x-www-form-urlencoded
  data = qs.stringify(data);
  // get the url from the form's action attribute
  const url = $(formEl).attr('action');

  // set request headers
  const myHeaders = new Headers();
  myHeaders.append('Content-Type', 'application/x-www-form-urlencoded');

  // set config object for fetch
  const config = {
    method: 'POST',
    headers: myHeaders,
    body: data
  };
  
  // do a body swap
  await bodySwapWithUrl(url, config);
  // re-bind the link events
  initClickEvent()
}

/**
 * Does an initial binding for ajax links. Adds an event listener to handle the
 * back button.
 */
function initAjaxLinks() {
  initClickEvent();

  $(window).on('popstate',  async (event) => {
    const url = event.target.location;
    await bodySwapWithUrl(url);
    // re-bind ajax links
    initClickEvent();
  });
}

/**
 * Attaches event listeners for all links with the `data-ajax-link` attribute.
 */
function initClickEvent() {
  // handle link clicks
  const $links = $('[data-ajax-link]').one('click', clickHandler);
}

/**
 * Click handler for ajax-enabled links. Does a fetch on the link's `href` URL
 * and does a body swap with the response.
 * 
 * @param {Event} event 
 */
async function clickHandler(event) {
  event.preventDefault();
  const url = $(event.target).attr('href');
  await bodySwapWithUrl(url);  
  
  // push history
  window.history.pushState({}, $('title').text(), url);

  // re-bind the click handler for ajax links
  initClickEvent();
}

/**
 * Uses fetch to make a request and swaps the body with the response text.
 * 
 * @param {string} url
 * @param {Object} config
 */
async function bodySwapWithUrl(url, config = undefined) {
  try {
    // request for the url
    const resp = await fetch(url, config);
    // get text of response
    const text = await resp.text();
    // body swap
    bodySwapWithHtml(text);
  } catch (err) {
    console.error(err);
  }
}

/**
 * Performs a "body" swap using the passed-in HTML. It isn't actually all the
 * content of the body tag, but rather everything inside the .ajax-wrapper
 * <div>.
 *
 * @param {string} html 
 */
export function bodySwapWithHtml(html) {
  // get the contents of the new page
  const $wrapper = $(`<div>${html}</div>`).find('.ajax-wrapper');
  // body swap
  $('.ajax-wrapper').html($wrapper.html());

  // emit an event to signal body swap complete
  $(window).trigger('postbodyswap');
}

/**
 * Given a URL, performs a fetch request and updates all CSRF tokens. Returns
 * the textual response of the fetch request.
 *
 * @param {string} url the url to fetch
 * @returns the text of the requested url's response
 */
export async function getUrl(url) {
  try {
    // get the response as text
    const resp = await fetch(url);
    const text = await resp.text();

    // get the csrf token
    const csrf = $(text).find('[name=csrf]').val();
    // replace all instances with the new csrf token
    $('[name=csrf]').val(csrf);

    // return the text response
    return text;
  } catch (err) {
    console.error(err);
  }
}