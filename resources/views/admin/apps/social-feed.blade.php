@extends("shared.base", ["title" => "Social Feed"])

@section("styles")
@endsection

@section("content")
    <div class="wrapper">
        @include("shared.partials.topbar") @include("shared.partials.sidenav")

        <div class="content-page">
            <div class="container-fluid">
                @include("shared.partials.page-title", ["subtitle" => "Apps", "title" => "Social Feed"])

                <div class="row">
                    <div class="col-xl-3 col-lg-6 order-lg-1 order-xl-1">
                        <div class="card card-top-sticky">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-2 position-relative">
                                        <img alt="avatar" class="rounded" height="42" src="/images/users/user-3.jpg" width="42" />
                                    </div>
                                    <div>
                                        <h5 class="mb-0 d-flex align-items-center">
                                            <a class="link-reset" href="#!">Geneva K.</a>
                                            <img alt="US" class="ms-2 rounded-circle" height="16" src="/images/flags/us.svg" />
                                        </h5>
                                        <p class="text-muted mb-0">Content Creator</p>
                                    </div>
                                    <div class="ms-auto">
                                        <div class="dropdown">
                                            <a class="btn btn-icon btn-ghost-light text-muted" data-bs-toggle="dropdown" href="#">
                                                <i class="fs-24" data-lucide="ellipsis-vertical"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="#">View Profile</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#">Send Message</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#">Copy Profile Link</a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider" />
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#">Edit Profile</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="#">Block User</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="#">Report User</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="list-group list-group-flush list-custom mt-3">
                                    <a class="list-group-item list-group-item-action active" href="#!">
                                        <i class="me-1 opacity-75 fs-lg align-middle" data-lucide="house-wifi"></i>
                                        <span class="align-middle">News Feed</span>
                                    </a>
                                    <a class="list-group-item list-group-item-action" href="#!">
                                        <i class="me-1 opacity-75 fs-lg align-middle" data-lucide="message-circle"></i>
                                        <span class="align-middle">Messages</span>
                                        <span class="badge align-middle bg-danger-subtle fs-xxs text-danger float-end">5</span>
                                    </a>
                                    <a class="list-group-item list-group-item-action" href="#!">
                                        <i class="me-1 opacity-75 fs-lg align-middle" data-lucide="users"></i>
                                        <span class="align-middle">Friends</span>
                                    </a>
                                    <a class="list-group-item list-group-item-action" href="#!">
                                        <i class="me-1 opacity-75 fs-lg align-middle" data-lucide="bell"></i>
                                        <span class="align-middle">Notifications</span>
                                        <span class="badge align-middle bg-warning-subtle text-warning fs-xxs float-end">12</span>
                                    </a>
                                    <a class="list-group-item list-group-item-action" href="#!">
                                        <i class="me-1 opacity-75 fs-lg align-middle" data-lucide="layout-grid"></i>
                                        <span class="align-middle">Groups</span>
                                    </a>
                                    <a class="list-group-item list-group-item-action" href="#!">
                                        <i class="me-1 opacity-75 fs-lg align-middle" data-lucide="book-open"></i>
                                        <span class="align-middle">Pages</span>
                                    </a>
                                    <a class="list-group-item list-group-item-action" href="#!">
                                        <i class="me-1 opacity-75 fs-lg align-middle" data-lucide="calendar-check"></i>
                                        <span class="align-middle">Events</span>
                                    </a>
                                    <a class="list-group-item list-group-item-action" href="#!">
                                        <i class="me-1 opacity-75 fs-lg align-middle" data-lucide="settings"></i>
                                        <span class="align-middle">Settings</span>
                                    </a>
                                    <div class="list-group-item mt-2">
                                        <span class="align-middle">Categories</span>
                                    </div>
                                    <a class="list-group-item list-group-item-action" href="#!">
                                        <i class="me-1 text-primary fs-lg align-middle" data-lucide="tag"></i>
                                        <span class="align-middle">Technology</span>
                                    </a>
                                    <a class="list-group-item list-group-item-action" href="#!">
                                        <i class="me-1 text-success fs-lg align-middle" data-lucide="tag"></i>
                                        <span class="align-middle">Travel</span>
                                    </a>
                                    <a class="list-group-item list-group-item-action" href="#!">
                                        <i class="me-1 text-danger fs-lg align-middle" data-lucide="tag"></i>
                                        <span class="align-middle">Lifestyle</span>
                                    </a>
                                    <a class="list-group-item list-group-item-action" href="#!">
                                        <i class="me-1 fs-lg align-middle text-info" data-lucide="tag"></i>
                                        <span class="align-middle">Photography</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-12 order-lg-2 order-xl-1">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="mb-2">What's on your mind?</h5>
                                <form action="#">
                                    <textarea class="form-control" placeholder="Share your thoughts..." rows="3"></textarea>
                                    <div class="d-flex pt-2 justify-content-between align-items-center">
                                        <div class="d-flex gap-1">
                                            <a class="btn btn-sm btn-icon btn-light" href="#">
                                                <i class="fs-md" data-lucide="user"></i>
                                            </a>
                                            <a class="btn btn-sm btn-icon btn-light" href="#">
                                                <i class="fs-md" data-lucide="map-pin"></i>
                                            </a>
                                            <a class="btn btn-sm btn-icon btn-light" href="#">
                                                <i class="fs-md" data-lucide="camera"></i>
                                            </a>
                                            <a class="btn btn-sm btn-icon btn-light" href="#">
                                                <i class="fs-md" data-lucide="smile"></i>
                                            </a>
                                        </div>
                                        <button class="btn btn-dark btn-sm" type="submit">Post</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body pb-2">
                                <div class="d-flex align-items-center mb-2">
                                    <img alt="Generic placeholder image" class="me-2 avatar-md rounded-circle" src="/images/users/user-10.jpg" />
                                    <div class="w-100">
                                        <h5 class="m-0">
                                            <a class="link-reset" href="#!">Jeremy Tomlinson</a>
                                        </h5>
                                        <p class="text-muted mb-0">
                                            <small>about 2 minutes ago</small>
                                        </p>
                                    </div>
                                    <div class="dropdown ms-auto">
                                        <a class="dropdown-toggle text-muted drop-arrow-none card-drop p-0" data-bs-toggle="dropdown" href="#">
                                            <i class="fs-lg" data-lucide="ellipsis-vertical"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="#">
                                                <i class="me-2" data-lucide="square-pen"></i>
                                                Edit Post
                                            </a>
                                            <a class="dropdown-item" href="#">
                                                <i class="me-2" data-lucide="trash-2"></i>
                                                Delete Post
                                            </a>
                                            <a class="dropdown-item" href="#">
                                                <i class="me-2" data-lucide="share-2"></i>
                                                Share
                                            </a>
                                            <a class="dropdown-item" href="#">
                                                <i class="me-2" data-lucide="pin"></i>
                                                Pin to Top
                                            </a>
                                            <a class="dropdown-item" href="#">
                                                <i class="me-2" data-lucide="flag"></i>
                                                Report Post
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <p>Story based around the idea of time lapse, animation to post soon!</p>
                                <div class="row g-1">
                                    <div class="col-md-6">
                                        <img alt="Tall Image" class="img-fluid w-100 h-100 rounded" src="/images/gallery/10.jpg" style="aspect-ratio: 3/4; object-fit: cover" />
                                    </div>
                                    <div class="col-md-6 d-flex flex-column gap-1">
                                        <img alt="Top Right" class="img-fluid w-100 rounded" src="/images/gallery/2.jpg" style="aspect-ratio: 4/3; object-fit: cover" />
                                        <img alt="Bottom Right" class="img-fluid w-100 rounded" src="/images/gallery/3.jpg" style="aspect-ratio: 4/3; object-fit: cover" />
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <a class="btn btn-sm fs-sm btn-link text-muted" href="javascript: void(0);">
                                        <i class="me-1" data-lucide="corner-up-left"></i>
                                        Reply
                                    </a>
                                    <span class="btn btn-sm fs-sm btn-link text-muted" data-toggler="on">
                                        <span class="align-middle" data-toggler-on="">
                                            <i class="text-danger" data-lucide="heart"></i>
                                            Liked!
                                        </span>
                                        <span class="d-none align-middle" data-toggler-off="">
                                            <i class="text-muted" data-lucide="heart"></i>
                                            Like
                                        </span>
                                    </span>
                                    <a class="btn btn-sm fs-sm btn-link text-muted" href="javascript: void(0);">
                                        <i class="me-1" data-lucide="share-2"></i>
                                        Share
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body pb-2">
                                <div class="d-flex align-items-center mb-2">
                                    <img alt="Generic placeholder image" class="me-2 avatar-sm rounded-circle" src="/images/users/user-4.jpg" />
                                    <div class="w-100">
                                        <h5 class="m-0">
                                            <a class="link-reset" href="#!">Sophia Martinez</a>
                                        </h5>
                                        <p class="text-muted mb-0">
                                            <small>about 30 minutes ago</small>
                                        </p>
                                    </div>
                                    <div class="dropdown ms-auto">
                                        <a class="dropdown-toggle text-muted drop-arrow-none card-drop p-0" data-bs-toggle="dropdown" href="#">
                                            <i class="fs-lg" data-lucide="ellipsis-vertical"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="#">
                                                <i class="me-2" data-lucide="square-pen"></i>
                                                Edit Post
                                            </a>
                                            <a class="dropdown-item" href="#">
                                                <i class="me-2" data-lucide="trash-2"></i>
                                                Delete Post
                                            </a>
                                            <a class="dropdown-item" href="#">
                                                <i class="me-2" data-lucide="share-2"></i>
                                                Share
                                            </a>
                                            <a class="dropdown-item" href="#">
                                                <i class="me-2" data-lucide="pin"></i>
                                                Pin to Top
                                            </a>
                                            <a class="dropdown-item" href="#">
                                                <i class="me-2" data-lucide="flag"></i>
                                                Report Post
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="fs-16 text-center mt-3 mb-4 fst-italic">
                                    <i class="fs-20" data-lucide="quote"></i>
                                    Just finished a weekend project! Built a small weather app using React and OpenWeather API. Feeling excited to share the results with everyone soon. 🚀
                                </div>
                                <div class="bg-light-subtle mx-n3 p-3 border-top border-bottom border-dashed">
                                    <div class="d-flex align-items-start">
                                        <img alt="Generic placeholder image" class="me-2 avatar-sm rounded-circle" src="/images/users/user-1.jpg" />
                                        <div class="w-100">
                                            <h5 class="mt-0 mb-1">
                                                <a class="link-reset" href="#!">Liam Johnson</a>
                                                <small class="text-muted fw-normal float-end">10 minutes ago</small>
                                            </h5>
                                            That sounds awesome! Can't wait to see how you designed the UI.
                                            <br />
                                            <a class="text-muted font-13 d-inline-block mt-2" href="javascript:void(0);">
                                                <i data-lucide="corner-up-left"></i>
                                                Reply
                                            </a>
                                            <div class="d-flex align-items-start mt-3">
                                                <a class="pe-2" href="#">
                                                    <img alt="Generic placeholder image" class="avatar-sm rounded-circle" src="/images/users/user-2.jpg" />
                                                </a>
                                                <div class="w-100">
                                                    <h5 class="mt-0 mb-1">
                                                        <a class="link-reset" href="#!">Olivia Carter</a>
                                                        <small class="text-muted fw-normal float-end">5 minutes ago</small>
                                                    </h5>
                                                    I recently built something similar with Vue. Let's collaborate sometime!
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start mt-3">
                                        <a class="pe-2" href="#">
                                            <img alt="Generic placeholder image" class="rounded-circle" height="31" src="/images/users/user-3.jpg" />
                                        </a>
                                        <div class="w-100">
                                            <input class="form-control form-control-sm" id="simpleinput" placeholder="Add a comment..." type="text" />
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <a class="btn btn-sm fs-sm btn-link text-muted" href="javascript: void(0);">
                                        <i class="me-1" data-lucide="corner-up-left"></i>
                                        Reply
                                    </a>
                                    <span class="btn btn-sm fs-sm btn-link text-muted" data-toggler="off">
                                        <span class="d-none align-middle" data-toggler-on="">
                                            <i class="text-danger" data-lucide="heart"></i>
                                            Liked!
                                        </span>
                                        <span class="align-middle" data-toggler-off="">
                                            <i class="text-muted" data-lucide="heart"></i>
                                            Likes (45)
                                        </span>
                                    </span>
                                    <a class="btn btn-sm fs-sm btn-link text-muted" href="javascript: void(0);">
                                        <i class="me-1" data-lucide="share-2"></i>
                                        Share
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body pb-2">
                                <div class="d-flex align-items-center mb-2">
                                    <img alt="Profile photo of Anika Roy" class="me-2 avatar-sm rounded-circle" src="/images/users/user-2.jpg" />
                                    <div class="w-100">
                                        <h5 class="m-0">
                                            <a class="link-reset" href="#!">Anika Roy</a>
                                        </h5>
                                        <p class="text-muted mb-0">
                                            <small>2 hours ago</small>
                                        </p>
                                    </div>
                                    <div class="dropdown ms-auto">
                                        <a class="dropdown-toggle text-muted drop-arrow-none card-drop p-0" data-bs-toggle="dropdown" href="#">
                                            <i class="fs-lg" data-lucide="ellipsis-vertical"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="#">
                                                <i class="me-2" data-lucide="square-pen"></i>
                                                Edit Post
                                            </a>
                                            <a class="dropdown-item" href="#">
                                                <i class="me-2" data-lucide="trash-2"></i>
                                                Delete Post
                                            </a>
                                            <a class="dropdown-item" href="#">
                                                <i class="me-2" data-lucide="share-2"></i>
                                                Share
                                            </a>
                                            <a class="dropdown-item" href="#">
                                                <i class="me-2" data-lucide="pin"></i>
                                                Pin to Top
                                            </a>
                                            <a class="dropdown-item" href="#">
                                                <i class="me-2" data-lucide="flag"></i>
                                                Report Post
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <p>Sharing a couple of timelapses from my recent Iceland trip. Let me know which one you like most!</p>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="ratio ratio-16x9 rounded overflow-hidden">
                                            <iframe allowfullscreen="" src="https://player.vimeo.com/video/1084537"></iframe>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="ratio ratio-16x9 rounded overflow-hidden">
                                            <iframe allowfullscreen="" src="https://player.vimeo.com/video/76979871"></iframe>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <a class="btn btn-sm fs-sm btn-link text-muted" href="javascript: void(0);">
                                        <i class="me-1" data-lucide="corner-up-left"></i>
                                        Reply
                                    </a>
                                    <span class="btn btn-sm fs-sm btn-link text-muted" data-toggler="on">
                                        <span class="align-middle" data-toggler-on="">
                                            <i class="text-danger" data-lucide="heart"></i>
                                            Liked!
                                        </span>
                                        <span class="d-none align-middle" data-toggler-off="">
                                            <i class="text-muted" data-lucide="heart"></i>
                                            Like
                                        </span>
                                    </span>
                                    <a class="btn btn-sm fs-sm btn-link text-muted" href="javascript: void(0);">
                                        <i class="me-1" data-lucide="share-2"></i>
                                        Share
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <img alt="Profile photo of David Kim" class="me-2 avatar-sm rounded-circle" src="/images/users/user-6.jpg" />
                                    <div class="w-100">
                                        <h5 class="m-0">
                                            <a class="link-reset" href="#!">David Kim</a>
                                        </h5>
                                        <p class="text-muted mb-0">
                                            <small>Posted 1 hour ago</small>
                                        </p>
                                    </div>
                                    <div class="dropdown ms-auto">
                                        <a class="dropdown-toggle text-muted drop-arrow-none card-drop p-0" data-bs-toggle="dropdown" href="#">
                                            <i class="fs-lg" data-lucide="ellipsis-vertical"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="#">
                                                <i class="me-2" data-lucide="square-pen"></i>
                                                Edit Post
                                            </a>
                                            <a class="dropdown-item" href="#">
                                                <i class="me-2" data-lucide="trash-2"></i>
                                                Delete Post
                                            </a>
                                            <a class="dropdown-item" href="#">
                                                <i class="me-2" data-lucide="share-2"></i>
                                                Share
                                            </a>
                                            <a class="dropdown-item" href="#">
                                                <i class="me-2" data-lucide="pin"></i>
                                                Pin to Top
                                            </a>
                                            <a class="dropdown-item" href="#">
                                                <i class="me-2" data-lucide="flag"></i>
                                                Report Post
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <h5 class="mb-3">🔥 Quick Poll: What’s your go-to front-end framework in 2025?</h5>
                                <p class="text-muted">We’re gathering developer preferences for our next project. Cast your vote below! 💻</p>
                                <form>
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" id="optionReact" name="framework_poll" type="radio" />
                                        <label class="form-check-label" for="optionReact">React (Meta)</label>
                                    </div>
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" id="optionVue" name="framework_poll" type="radio" />
                                        <label class="form-check-label" for="optionVue">Vue.js (Evan You)</label>
                                    </div>
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" id="optionAngular" name="framework_poll" type="radio" />
                                        <label class="form-check-label" for="optionAngular">Angular (Google)</label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" id="optionSvelte" name="framework_poll" type="radio" />
                                        <label class="form-check-label" for="optionSvelte">Svelte (Emerging Favorite)</label>
                                    </div>
                                    <button class="btn btn-sm btn-primary" type="submit">Submit Vote</button>
                                </form>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <img alt="Profile photo of Anika Roy" class="me-2 avatar-sm rounded-circle" src="/images/users/user-2.jpg" />
                                    <div class="w-100">
                                        <h5 class="m-0">
                                            <a class="link-reset" href="#!">Anika Roy</a>
                                        </h5>
                                        <p class="text-muted mb-0">
                                            <small>Posted 2 hours ago</small>
                                        </p>
                                    </div>
                                    <div class="dropdown ms-auto">
                                        <a class="dropdown-toggle text-muted drop-arrow-none card-drop p-0" data-bs-toggle="dropdown" href="#">
                                            <i class="fs-lg" data-lucide="ellipsis-vertical"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="#">
                                                <i class="me-2" data-lucide="square-pen"></i>
                                                Edit Post
                                            </a>
                                            <a class="dropdown-item" href="#">
                                                <i class="me-2" data-lucide="trash-2"></i>
                                                Delete Post
                                            </a>
                                            <a class="dropdown-item" href="#">
                                                <i class="me-2" data-lucide="share-2"></i>
                                                Share
                                            </a>
                                            <a class="dropdown-item" href="#">
                                                <i class="me-2" data-lucide="pin"></i>
                                                Pin to Top
                                            </a>
                                            <a class="dropdown-item" href="#">
                                                <i class="me-2" data-lucide="flag"></i>
                                                Report Post
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <h5 class="mb-2">
                                    📢 You're Invited:
                                    <strong>Dev Meetup 2025 – Build with AI</strong>
                                </h5>
                                <p class="text-muted mb-2">Join developers and tech enthusiasts for an inspiring evening of AI-driven development talks, live demos, and networking opportunities.</p>
                                <ul class="list-unstyled mb-3">
                                    <li class="pb-2">
                                        <strong>Date:</strong>
                                        Friday, 25th July 2025
                                    </li>
                                    <li class="pb-2">
                                        <strong>Time:</strong>
                                        6:00 PM IST
                                    </li>
                                    <li>
                                        <strong>Location:</strong>
                                        Online (Zoom – link to be shared)
                                    </li>
                                </ul>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="me-1" data-lucide="bell"></i>
                                        Interested
                                    </button>
                                    <button class="btn btn-sm btn-primary">
                                        <i class="me-1" data-lucide="user-plus"></i>
                                        Join Now
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body text-center">
                                <h1 class="mb-2">🏆</h1>
                                <h4 class="mb-1 fw-semibold">Congratulations, Anika! 🎉</h4>
                                <p class="text-muted fst-italic mb-3">
                                    You’ve hit
                                    <strong>1,000 followers</strong>
                                    ! Your content is making waves in the community!
                                </p>
                                <div class="d-flex justify-content-center mb-3">
                                    <div class="me-4 text-center">
                                        <h6 class="mb-0">Posts</h6>
                                        <span class="fw-bold">135</span>
                                    </div>
                                    <div class="me-4 text-center">
                                        <h6 class="mb-0">Likes</h6>
                                        <span class="fw-bold">8,400</span>
                                    </div>
                                    <div class="text-center">
                                        <h6 class="mb-0">Followers</h6>
                                        <span class="fw-bold">1,000</span>
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-outline-success me-2">
                                    <i class="me-1" data-lucide="share-2"></i>
                                    Share Achievement
                                </button>
                                <a class="btn btn-sm btn-primary" href="#!">
                                    <i class="me-1" data-lucide="user"></i>
                                    View Profile
                                </a>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-center gap-2 p-3 mb-3">
                            <strong>Loading...</strong>
                            <div aria-hidden="true" class="spinner-border spinner-border-sm text-danger" role="status"></div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 order-lg-1 order-xl-2">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Activity</h5>
                                    <a class="link-reset fs-sm" href="#">See all</a>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted text-uppercase">Stories About You</small>
                                    <div class="d-flex align-items-center mt-2">
                                        <img alt="mention" class="rounded-circle me-2" height="32" src="/images/users/user-7.jpg" width="32" />
                                        <div>
                                            <strong>Mentions</strong>
                                            <br />
                                            <span class="text-muted fs-xs">3 stories mention you</span>
                                        </div>
                                    </div>
                                </div>
                                <span class="text-muted fs-xs fw-bold text-uppercase">New</span>
                                <ul class="list-unstyled mt-2 mb-0">
                                    <li class="d-flex align-items-center py-1">
                                        <img alt="jenny.w" class="rounded-circle me-2" height="36" src="/images/users/user-8.jpg" width="36" />
                                        <div class="flex-grow-1">
                                            <strong>jenny.w</strong>
                                            started following you
                                            <br />
                                            <span class="text-muted fs-xs">2m ago</span>
                                        </div>
                                        <div class="text-primary">
                                            <i class="fs-lg" data-lucide="user-plus"></i>
                                        </div>
                                    </li>
                                    <li class="d-flex align-items-center py-1">
                                        <img alt="daniel92" class="rounded-circle me-2" height="36" src="/images/users/user-9.jpg" width="36" />
                                        <div class="flex-grow-1">
                                            <strong>daniel92</strong>
                                            commented on your post
                                            <br />
                                            <span class="text-muted fs-xs">3m ago</span>
                                        </div>
                                        <div>
                                            <img alt="commented" class="rounded" height="32" src="/images/gallery/1.jpg" width="32" />
                                        </div>
                                    </li>
                                    <li class="d-flex align-items-center py-1">
                                        <img alt="amelie.design" class="rounded-circle me-2" height="36" src="/images/users/user-10.jpg" width="36" />
                                        <div class="flex-grow-1">
                                            <strong>amelie.design</strong>
                                            liked your story
                                            <br />
                                            <span class="text-muted fs-xs">4m ago</span>
                                        </div>
                                        <div>
                                            <img alt="liked" class="rounded" height="32" src="/images/gallery/2.jpg" width="32" />
                                        </div>
                                    </li>
                                    <li class="d-flex align-items-center py-1">
                                        <img alt="johnny_dev" class="rounded-circle me-2" height="36" src="/images/users/user-5.jpg" width="36" />
                                        <div class="flex-grow-1">
                                            <strong>johnny_dev</strong>
                                            started following you
                                            <br />
                                            <span class="text-muted fs-xs">6m ago</span>
                                        </div>
                                        <div class="text-primary">
                                            <i class="fs-lg" data-lucide="user-plus"></i>
                                        </div>
                                    </li>
                                    <li class="d-flex align-items-center py-1">
                                        <img alt="art.gal" class="rounded-circle me-2" height="36" src="/images/users/user-6.jpg" width="36" />
                                        <div class="flex-grow-1">
                                            <strong>art.gal</strong>
                                            liked your post
                                            <br />
                                            <span class="text-muted fs-xs">8m ago</span>
                                        </div>
                                        <div>
                                            <img alt="liked" class="rounded" height="32" src="/images/gallery/3.jpg" width="32" />
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header justify-content-between align-items-center border-dashed">
                                <h4 class="card-title mb-0">Trending</h4>
                                <div class="dropdown">
                                    <a class="dropdown-toggle text-muted drop-arrow-none card-drop p-0" data-bs-toggle="dropdown" href="#">
                                        <i class="fs-lg" data-lucide="ellipsis-vertical"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="#">
                                            <i class="me-2" data-lucide="refresh-ccw"></i>
                                            Refresh Feed
                                        </a>
                                        <a class="dropdown-item" href="#">
                                            <i class="me-2" data-lucide="settings"></i>
                                            Manage Topics
                                        </a>
                                        <a class="dropdown-item" href="#">
                                            <i class="me-2" data-lucide="circle-alert"></i>
                                            Report Issue
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex mb-3">
                                    <i class="text-primary me-2 mt-1" data-lucide="trending-up"></i>
                                    <a class="link-reset text-decoration-none" href="#!">
                                        <strong>Golden Globes:</strong>
                                        The 27 Best moments from the Golden Globe Awards
                                    </a>
                                </div>
                                <div class="d-flex mb-3">
                                    <i class="text-primary me-2 mt-1" data-lucide="trending-up"></i>
                                    <a class="link-reset text-decoration-none" href="#!">
                                        <strong>World Cricket:</strong>
                                        India has won ICC T20 World Cup Yesterday
                                    </a>
                                </div>
                                <div class="d-flex mb-3">
                                    <i class="text-primary me-2 mt-1" data-lucide="trending-up"></i>
                                    <a class="link-reset text-decoration-none" href="#!">
                                        <strong>Antarctica:</strong>
                                        Melting of Totten Glacier could cause high risk to areas near by sea
                                    </a>
                                </div>
                                <div class="d-flex">
                                    <i class="text-primary me-2 mt-1" data-lucide="trending-up"></i>
                                    <a class="link-reset text-decoration-none" href="#!">
                                        <strong>Global Tournament:</strong>
                                        America has won Football match Yesterday
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header justify-content-between align-items-center border-dashed">
                                <h4 class="card-title mb-0">Requests</h4>
                                <div class="dropdown">
                                    <a class="dropdown-toggle text-muted drop-arrow-none card-drop p-0" data-bs-toggle="dropdown" href="#">
                                        <i class="fs-lg" data-lucide="ellipsis-vertical"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="#">
                                            <i class="me-2" data-lucide="check"></i>
                                            Mark All as Read
                                        </a>
                                        <a class="dropdown-item" href="#">
                                            <i class="me-2" data-lucide="trash-2"></i>
                                            Clear All
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-start">
                                        <img alt="Emily Zhang" class="avatar-xs rounded-circle me-2" src="/images/users/user-3.jpg" />
                                        <div>
                                            <p class="mb-1">
                                                <strong>Emily Zhang</strong>
                                                requested to collaborate on your design project.
                                                <span class="badge bg-primary ms-1">New</span>
                                            </p>
                                            <small class="text-muted">2 minutes ago</small>
                                        </div>
                                    </div>
                                    <button class="btn btn-sm py-0 px-1 btn-default">Accept</button>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-start gap-2">
                                        <div class="avatar-xs flex-shrink-0">
                                            <span class="avatar-title text-bg-info rounded-circle">
                                                <i data-lucide="rocket"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <p class="mb-1">
                                                <strong>New Feature:</strong>
                                                Suggestion for dark mode support.
                                                <span class="badge bg-warning text-dark ms-1">Pending</span>
                                            </p>
                                            <small class="text-muted">10 minutes ago</small>
                                        </div>
                                    </div>
                                    <button class="btn btn-sm py-0 px-1 btn-default">Review</button>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-start">
                                        <img alt="John Doe" class="avatar-xs rounded-circle me-2" src="/images/users/user-6.jpg" />
                                        <div>
                                            <p class="mb-1">
                                                <strong>Client Feedback:</strong>
                                                John Doe left a review on your dashboard.
                                                <span class="badge bg-secondary ms-1">Feedback</span>
                                            </p>
                                            <small class="text-muted">30 minutes ago</small>
                                        </div>
                                    </div>
                                    <button class="btn btn-sm py-0 px-1 btn-default">Respond</button>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-start gap-2">
                                        <div class="avatar-xs flex-shrink-0">
                                            <span class="avatar-title text-bg-primary rounded-circle">
                                                <i data-lucide="bug"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <p class="mb-1">
                                                <strong>Bug Report:</strong>
                                                Login form issue on Safari mobile.
                                                <span class="badge bg-danger ms-1">Urgent</span>
                                            </p>
                                            <small class="text-muted">1 hour ago</small>
                                        </div>
                                    </div>
                                    <button class="btn btn-sm py-0 px-1 btn-default">View</button>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header justify-content-between align-items-center border-dashed">
                                <h4 class="card-title mb-0">Featured Video For You</h4>
                                <div class="dropdown">
                                    <a class="dropdown-toggle text-muted drop-arrow-none card-drop p-0" data-bs-toggle="dropdown" href="#">
                                        <i class="fs-lg" data-lucide="ellipsis-vertical"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="#">Watch Later</a>
                                        <a class="dropdown-item" href="#">Report Video</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="ratio ratio-16x9 rounded overflow-hidden">
                                    <iframe allowfullscreen="" src="https://player.vimeo.com/video/357274789"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include("shared.partials.footer")
        </div>
    </div>

    @include("shared.partials.customizer") @include("shared.partials.footer-scripts")
@endsection

@section("scripts")
@endsection
