export const SiteNavigation = props => {
    const pages_visitted = props.user.meta.page_visits || [
        {url: props.user.meta.current_url}
    ];

    return  (
        <div className="border-0 border-bottom border-secondary mb-2">
            <div className="mb-2">
                <h6>Site Navigation</h6>
                {pages_visitted.map((page, i) => <div key={i} className="mb-2">
                    	<a target="_blank" href={page.url} className="xs">{page.title}</a>
                </div>)}
            </div>
        </div>
    );
}
