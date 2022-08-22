/**
 * define the resource schema
 */

import sr from './sr.json'

// define the locale message schema as master
export type MessageSchema = typeof sr;

// define the number format schema
export type NumberSchema = {
  currency: {
    style: 'currency'
    currencyDisplay: 'symbol'
    currency: string
  }
}
