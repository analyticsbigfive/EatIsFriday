// Types for pages content
export interface HeroTitle {
  line_1: string
  line_2_prefix?: string
  line_2_highlight?: string
  line_2?: string
  line_3?: string
  line_3_highlight?: string
  line_4?: string
  highlight?: string
}

export interface CtaButton {
  text: string
  link: string
}

export interface ContentImage {
  src: string
  alt: string
}

export interface Service {
  title: string
  description: string
  icon?: string
  bg_color?: string
  text_color?: string
  thumbnail?: string
  btnImage?: string
  linkTo?: string
}

export interface Location {
  name: string
  count: number
}

export interface MapVenue {
  id: string
  name: string
  location: string
  lat: number
  lng: number
  open_positions?: number
  type?: string
  image?: string
  image2?: string
  logo?: string
  capacity?: string
  staff_members?: number
  recent_event?: string
  guests_served?: string
  shops_count?: number
  menus_count?: number
  description?: string
}

export interface EventType {
  id: string
  name: string
  image: string
}

export interface Stat {
  value: string
  label: string
}

export interface HomepageCTA {
  title: string
  image: string
  description: string
  link: string
  button: string
  additionalText: string
  button2: string
  link2: string
}

export interface Partner {
  logo: string
  alt: string
}

export interface SustainableService {
  background: string
  icone: string
  title: string
  description: string
}

export interface Beautiful {
  title: string
  text: string
  image: string
}

export interface Example {
  title: string
  texte: string
  btn: string
  link: string
}

export interface HomepageContent {
  hero_section: {
    bg: string
    tag: string
    title: HeroTitle
    description: string
    cta_primary: CtaButton
    cta_secondary: CtaButton
    images: ContentImage[]
    experience_badge: {
      number: string
      label: string
    }
    floating_badge: string
  }
  intro_section: {
    texte: string
  }
  locations_section: {
    title: string
    description: string
    filter_label: string
    event_types: EventType[]
    stats: Stat[]
    map_venues: MapVenue[]
  }
  services_section: {
    tag: string
    title: HeroTitle
    services: Service[]
    learn_more_button: string
  }
  cta_section: {
    title: string
    description: string
    cta_primary?: CtaButton
    cta_secondary?: CtaButton
  }
  gallery_section: {
    images: ContentImage[]
  }
  sustainable_service_title: string
  sustainable_service: SustainableService[]
  beautiful: Beautiful
  examples: Example[]
  partners_title: string
  partners: Partner[]
  homepageCTA: HomepageCTA
}

export interface CareersContent {
  map_loading: string
  hero_default: {
    title_line_1: string
    title_line_2: string
    title_line_3: string
    image: string
    background_image: string
  }
  join_box?: {
    title: string
    description: string
  }
  hero_with_venue: {
    tag: string
    title_prefix: string
    subtitle: string
    stats: {
      open_positions_label: string
      locations_label: string
    }
  }
  hero_section: {
    tag: string
    title_template: string
    open_positions_suffix: string
  }
  search_section: {
    search_placeholder: string
    all_sites_label: string
    job_types: string[]
  }
  job_listing: {
    positions_available_singular: string
    positions_available_plural: string
    positions_available_suffix: string
    posted_prefix: string
    department_prefix: string
    apply_button: string
    view_details_button: string
  }
  no_results: {
    title: string
    description: string
    clear_filters_button: string
  }
  cta_section: {
    title: string
    description: string
    explore_venues_button: string
    general_application_button: string
  }
  venues: MapVenue[]
  jobs: Array<{
    id: string
    title: string
    department: string
    type: string
    salary: string
    description: string
    posted_time: string
    venue_id: string
  }>
}

export interface ContactContent {
  hero_section: {
    title: HeroTitle
    description: string
    image: ContentImage
  }
  form: {
    name_placeholder: string
    email_placeholder: string
    event_type_placeholder: string
    location_placeholder: string
    date_placeholder: string
    guests_placeholder: string
    message_placeholder: string
    submit_button: string
  }
}

export interface NavbarContent {
  brand_name: string
  brand_highlight: string
  nav_links: Array<{ text: string; to: string }>
  cta_desktop: string
  cta_mobile: string
}

export interface FooterContent {
  logoFooter: string
  brand_name: string
  brand_description: string
  contact_email: string
  contact_phone: string
  company_title: string
  company_links: Array<{ text: string; to: string }>
  policy_title: string
  policy_links: Array<{ text: string; to: string }>
  social_links: Array<{ icon: string; url: string }>
  copyright_template: string
  back_to_top: string
}

export interface FormsContent {
  contact_form: {
    name_label: string
    name_placeholder: string
    email_label: string
    email_placeholder: string
    subject_label: string
    subject_placeholder: string
    message_label: string
    message_placeholder: string
    submit_button: string
    submitting_button: string
    success_message: string
  }
  job_application_form: {
    title: string
    name_label: string
    name_placeholder: string
    email_label: string
    email_placeholder: string
    phone_label: string
    phone_placeholder: string
    resume_label: string
    cover_letter_label: string
    cover_letter_placeholder: string
    submit_button: string
    submitting_button: string
    success_message: string
  }
  activity_registration_form: {
    title: string
    name_label: string
    name_placeholder: string
    email_label: string
    email_placeholder: string
    phone_label: string
    phone_placeholder: string
    participants_label: string
    dietary_restrictions_label: string
    dietary_restrictions_placeholder: string
    additional_info_label: string
    additional_info_placeholder: string
    submit_button: string
    submitting_button: string
    success_message: string
  }
}

export interface PagesContent {
  homepage: HomepageContent
  careers: CareersContent
  contact: ContactContent
  events: any
  blog: any
  job_detail: any
  job_detail_slug: any
  apply_activities: any
  apply_jobs: any
  about: any
  components: {
    header: any
    navbar: NavbarContent
    contact_modal: any
    footer: FooterContent
    footer_alt: any
  }
  forms: FormsContent
  cards: any
  map: any
}

export const usePageContent = () => {
  const { fetchData, fetchLocalData } = useApi()

  /**
   * Deep merge two objects - WordPress data takes priority
   */
  const deepMerge = (target: any, source: any): any => {
    const result = { ...target }
    for (const key in source) {
      if (source[key] !== null && source[key] !== undefined && source[key] !== '') {
        if (typeof source[key] === 'object' && !Array.isArray(source[key])) {
          result[key] = deepMerge(target[key] || {}, source[key])
        } else {
          result[key] = source[key]
        }
      }
    }
    return result
  }

  /**
   * Map WordPress structure to Nuxt expected structure
   * WordPress returns: { hero: { title, subtitle } }
   * Nuxt expects: { hero_section: { title: { line_1, line_2 }, bg } }
   */
  const mapWordPressToNuxt = (wpData: any, localData: any): any => {
    if (!wpData) return localData
    if (!localData) return wpData

    const result = { ...localData }

    // Map homepage
    if (wpData.homepage?.hero) {
      result.homepage = result.homepage || {}
      result.homepage.hero_section = result.homepage.hero_section || {}
      
      // Map hero title - WordPress has flat title, Nuxt has structured title
      if (wpData.homepage.hero.title) {
        result.homepage.hero_section.title = result.homepage.hero_section.title || {}
        result.homepage.hero_section.title.line_1 = wpData.homepage.hero.title
      }
      if (wpData.homepage.hero.subtitle) {
        result.homepage.hero_section.title = result.homepage.hero_section.title || {}
        result.homepage.hero_section.title.line_3 = wpData.homepage.hero.subtitle
      }
      if (wpData.homepage.hero.background_image) {
        result.homepage.hero_section.bg = wpData.homepage.hero.background_image
      }
    }

    // Map about page
    if (wpData.about?.hero) {
      result.about = result.about || {}
      result.about.hero_section = result.about.hero_section || {}
      if (wpData.about.hero.title) {
        result.about.hero_section.title = wpData.about.hero.title
      }
      if (wpData.about.hero.subtitle) {
        result.about.hero_section.subtitle = wpData.about.hero.subtitle
      }
      if (wpData.about.hero.background_image) {
        result.about.hero_section.background_image = wpData.about.hero.background_image
      }
    }

    // Map contact page
    if (wpData.contact?.hero) {
      result.contact = result.contact || {}
      result.contact.hero_section = result.contact.hero_section || {}
      if (wpData.contact.hero.title) {
        result.contact.hero_section.title = result.contact.hero_section.title || {}
        result.contact.hero_section.title.line_1 = wpData.contact.hero.title
      }
      if (wpData.contact.hero.subtitle) {
        result.contact.hero_section.subtitle = wpData.contact.hero.subtitle
      }
    }

    // Map careers page
    if (wpData.careers?.hero) {
      result.careers = result.careers || {}
      result.careers.hero_default = result.careers.hero_default || {}
      if (wpData.careers.hero.title) {
        result.careers.hero_default.title_line_1 = wpData.careers.hero.title
      }
      if (wpData.careers.hero.subtitle) {
        result.careers.hero_default.title_line_2 = wpData.careers.hero.subtitle
      }
    }

    // Map events page
    if (wpData.events?.hero) {
      result.events = result.events || {}
      result.events.hero_section = result.events.hero_section || {}
      if (wpData.events.hero.title) {
        result.events.hero_section.title = wpData.events.hero.title
      }
      if (wpData.events.hero.subtitle) {
        result.events.hero_section.subtitle = wpData.events.hero.subtitle
      }
    }

    return result
  }

  /**
   * Get all pages content - ALWAYS fetches from WordPress API first
   * Then merges with local data for complete structure
   */
  const getPageContent = async (): Promise<PagesContent | null> => {
    // Always fetch local data for complete structure
    const localData = await fetchLocalData<PagesContent>('pages-content.json')
    
    // Fetch from WordPress API
    const wpData = await fetchData<any>('pages-content')
    
    if (wpData) {
      console.log('%c[PageContent] 🔄 Merging WordPress data with local structure', 'color: #FF4D6D;')
      return mapWordPressToNuxt(wpData, localData)
    }
    
    // If WordPress API fails, use local data
    console.log('%c[PageContent] ⚠️ WordPress API unavailable, using local data', 'color: orange;')
    return localData
  }

  /**
   * Get homepage content
   */
  const getHomepageContent = async (): Promise<HomepageContent | null> => {
    const content = await getPageContent()
    return content?.homepage || null
  }

  /**
   * Get careers page content
   */
  const getCareersContent = async (): Promise<CareersContent | null> => {
    const content = await getPageContent()
    return content?.careers || null
  }

  /**
   * Get contact page content
   */
  const getContactContent = async (): Promise<ContactContent | null> => {
    const content = await getPageContent()
    return content?.contact || null
  }

  const getNavbarContent = async (): Promise<NavbarContent | null> => {
    const content = await getPageContent()
    return content?.components?.navbar || null
  }

  const getFooterContent = async (): Promise<FooterContent | null> => {
    const content = await getPageContent()
    return content?.components?.footer || null
  }

  const getFormsContent = async (): Promise<FormsContent | null> => {
    const content = await getPageContent()
    return content?.forms || null
  }

  // Helper to get any section by path (e.g., 'homepage.hero_section')
  const getContentByPath = async <T>(path: string): Promise<T | null> => {
    const content = await getPageContent()
    if (!content) return null

    const keys = path.split('.')
    let result: any = content

    for (const key of keys) {
      if (result && typeof result === 'object' && key in result) {
        result = result[key]
      } else {
        return null
      }
    }

    return result as T
  }

  const getHeaderContent = async () => {
    const content = await getPageContent()
    return content?.components?.header || null
  }

  const getContactModalContent = async () => {
    const content = await getPageContent()
    return content?.components?.contact_modal || null
  }

  const getJobDetailContent = async () => {
    const content = await getPageContent()
    return content?.job_detail || null
  }

  const getJobDetailSlugContent = async () => {
    const content = await getPageContent()
    return content?.job_detail_slug || null
  }

  return {
    getPageContent,
    getHomepageContent,
    getCareersContent,
    getContactContent,
    getNavbarContent,
    getFooterContent,
    getFormsContent,
    getHeaderContent,
    getContactModalContent,
    getJobDetailContent,
    getJobDetailSlugContent,
    getContentByPath
  }
}
