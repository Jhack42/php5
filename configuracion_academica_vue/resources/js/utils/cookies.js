import Cookies from 'js-cookie';

export function getCookies(name) {
  const data = Cookies.get(name);
  return data ? JSON.parse(data) : null;
}

export function setCookies(name, value) {
  Cookies.set(name, JSON.stringify(value), { expires: 7 });
}
