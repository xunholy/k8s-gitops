package containers_image_tag

import data.lib.kubernetes

violation[msg] {
	kubernetes.containers[container]
	[image_name, "latest"] = split_image(container.image)
	msg = kubernetes.format(sprintf("%s in the %s %s has an image, %s, using the latest tag", [container.name, kubernetes.kind, image_name, kubernetes.name]))
}

split_image(image) = [image, "latest"] {
	not contains(image, ":")
}

split_image(image) = [image_name, tag] {
	[image_name, tag] = split(image, ":")
}
